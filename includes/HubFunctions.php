<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;


class HubFunctions{
    protected $showPositon;
    protected $hidePositon;

    public function __construct($atts) {
        // $this->sem = (!empty($atts['sem']) && self::checkSemester($atts['sem']) ? $atts['sem'] : '');
        $this->showPositon = (!empty($atts['zeige_jobs']) ? explode('|', $atts['zeige_jobs']) : []);
        $this->hidePositon = (!empty($atts['ignoriere_jobs']) ? explode('|', $atts['ignoriere_jobs']) : []);

        // echo 'in construct';
        // var_dump($this->hideJobs);
    }


    public function onLoaded() {
    }

    public function showPosition($position){
        // show is given => show matches only 
        if (!empty($this->showPositon) && !in_array($position, $this->showPositon)){
            return FALSE;
        }
        // hide defined jobs, show all others => config: ignoriere_jobs && shortcode: ignoriere_jobs
        if (!empty($this->hidePositon) && in_array($position, $this->hidePositon)){
            return FALSE;
        }
        return TRUE;
    }

    public function getPerson($aAtts){
        global $wpdb;
        $aRet = [];

        $prepare_vals = [
            $aAtts['filterValue']
        ];

        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM getPerson WHERE " . $aAtts['filterBy'] . " = %s" . (!empty($aAtts['orderBy'])?" ORDER BY " . $aAtts['orderBy']:''), $prepare_vals), ARRAY_A);
        if ($wpdb->last_error){
            echo json_encode($wpdb->last_error);
            exit;
        }

        $aLocations = [];
        $aOfficehours = [];
        $aGroup = [];

        foreach ($rows as $row) {
            $aRet[$row['ID']] = [
                'person_id' => $row['person_id'],
                'title' => $row['title'],
                'title_long' => $row['title_long'],
                'atitle' => $row['atitle'],
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
                'organization' => $row['organization'],
                'department' => $row['department'],
                'letter' => $row['letter']
            ];

            if (!(empty($row['email']) && empty($row['tel']) && empty($row['mobile']) && empty($row['street']) && empty($row['city']) && empty($row['office']))) {
                $aLocations[$row['ID']][$row['locationID']] = [
                    'email' => $row['email'],
                    'tel' => $row['tel'],
                    'tel_call' => $row['tel_call'],
                    'mobile' => $row['mobile'],
                    'mobile_call' => $row['mobile_call'],
                    'street' => $row['street'],
                    'city' => $row['city'],
                    'office' => $row['office']
                ];
                $aRet[$row['ID']]['locations'] = $aLocations[$row['ID']];
            }

            if (!(empty($row['repeat']) && empty($row['starttime']) && empty($row['endtime']) && empty($row['officehours_office']) && empty($row['comment']))){
                $aOfficehours[$row['ID']][$row['officehoursID']] = [
                    'repeat' => $row['repeat'],
                    'starttime' => $row['starttime'],
                    'endtime' => $row['endtime'],
                    'office' => $row['officehours_office'],
                    'comment' => $row['comment']
                ];
                $aRet[$row['ID']]['officehours'] = $aOfficehours[$row['ID']];
            }

            if (!empty($aAtts['groupBy'])){
                if ($aAtts['groupBy'] != 'position'){
                    $aGroup[$row[$aAtts['groupBy']]][$row['ID']] = $aRet[$row['ID']]; 
                }elseif ($this->showPosition($row['position'])){
                    $aGroup[$row[$aAtts['groupBy']]][$row['ID']] = $aRet[$row['ID']]; 
                }
            }
        }

        if (!empty($aAtts['groupBy'])){
            if ($aAtts['groupBy'] != 'position') {
                ksort($aGroup);
            }
            $aRet = $aGroup; 
        }

        return $aRet;
    }


    public function getLecture($aAtts){
        global $wpdb;
        $aRet = [];

        $prepare_vals = [
            $aAtts['filterValue']
        ];

        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM getLecture WHERE " . $aAtts['filterBy'] . " = %s", $prepare_vals), ARRAY_A);
        if ($wpdb->last_error) {
            echo json_encode($wpdb->last_error);
            exit;
        }

        // echo '<pre>';
        // var_dump($rows);
        // exit;


        $aLecturers = [];
        $aCourses = [];
        $aTerms = [];
        $aCourseLecturers = [];
        foreach($rows as $row){
            $aRet[$row['lecture_univisID']] = [
                'lecture_type' => $row['lecture_type'],
                'lecture_univisID' => $row['lecture_univisID'],
                'lecture_title' => $row['lecture_title'],
                'ects_name' => $row['ects_name'],
                'comment' => $row['comment'],
                'organizational' => $row['organizational'],
                'sws' => $row['sws'],
                'maxturnout' => $row['maxturnout'],
                'beginners' => $row['beginners'],
                'earlystudy' => $row['earlystudy'],
                'guest' => $row['guest'],
                'certification' => $row['certification'],
                'ects' => $row['ects'],
                'ects_cred' => $row['ects_cred'],
                'leclanguage_long' => $row['leclanguage'], 
                // 2DO: angaben
                // 2DO: 'studs'
            ];

            if (!empty($row['lecture_person_univisID'])) {
                $aLecturers[$row['lecture_univisID']][$row['lecture_person_univisID']] = [
                    'univisID' => $row['lecture_person_univisID'],
                    'title' => $row['lecture_person_title'],
                    'firstname' => $row['lecture_person_firstname'],
                    'lastname' => $row['lecture_person_lastname']
                ];

                if (!empty($aLecturers[$row['lecture_univisID']])) {
                    $aRet[$row['lecture_univisID']]['lecturers'] = $aLecturers[$row['lecture_univisID']];
                }
            }

            if (!empty($row['courseID'])){
                $aCourses[$row['lecture_univisID']][$row['courseID']] = [
                    'coursename' => $row['coursename']
                ];

                if (!empty($row['course_person_univisID'])) {
                    $aCourseLecturers[$row['courseID']][$row['course_person_univisID']] = [
                        'univisID' => $row['course_person_univisID'],
                        'title' => $row['course_person_title'],
                        'firstname' => $row['course_person_firstname'],
                        'lastname' => $row['course_person_lastname']
                    ];
                }

                if (!empty($row['termID'])){
                    $aTerms[$row['courseID']][$row['termID']] = [
                        'repeat' => $row['repeat'],
                        'starttime' => $row['term_starttime'],
                        'endtime' => $row['term_endtime'],
                        'room' => $row['room'],
                        'north' => $row['north'],
                        'east' => $row['east'],
                        'exclude' => $row['exclude']
                    ];
                }

                if (!empty($aCourses[$row['lecture_univisID']])){
                    if (!empty($aCourses[$row['lecture_univisID']][$row['courseID']])){
                        if (!empty($aCourseLecturers[$row['courseID']])){
                            $aCourses[$row['lecture_univisID']][$row['courseID']]['lecturers'] = $aCourseLecturers[$row['courseID']];
                        }
                        $aCourses[$row['lecture_univisID']][$row['courseID']]['terms'] = $aTerms[$row['courseID']];
                    }
            
                    $aRet[$row['lecture_univisID']]['courses'] = $aCourses[$row['lecture_univisID']];
                }
            }
        }

        // echo '<pre>';
        // var_dump($aRet);
        // exit;

        if (!empty($aAtts['groupBy'])){
            $aGroup = [];
            foreach($aRet as $row){
                $aGroup[$row[$aAtts['groupBy']]][$row['lecture_univisID']] = $row;
            }
            $aRet = $aGroup;
        }

        // sort by attribute "order"
        if (!empty($aAtts['orderBy'])){
            $aOrder = explode(',', $aAtts['orderBy']);
            $aSorted = [];
            foreach($aOrder as $order){
                foreach($aRet as $type => $lectures){
                    foreach($lectures as $lecture){
                        if ($lecture['lecture_type_short'] == trim($order)){
                            $aSorted[$type] = $aRet[$type];
                            unset($aRet[$type]);
                            break 1;
                        }
                    }
                }
            }
            $aRet = $aSorted;
        }


        echo '<pre>';
        var_dump($aRet);
        exit;

        return $aRet;

    }

}
