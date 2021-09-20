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

        $aGroup = [];

        if (!empty($aAtts['groupBy'])){
            foreach($rows as $row){
                $aGroup[$row[$aAtts['groupBy']]][$row['lecture_id']] = $row;
            }
            $aRet = $aGroup;
        }

        echo '<pre>';
        var_dump($aRet);
        exit;

        return $aRet;

    }

}
