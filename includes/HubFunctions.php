<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;


class HubFunctions{
    protected $pluginFile;
    protected $showJobs;
    protected $hideJobs;

    public function __construct($pluginFile) {
    }


    public function onLoaded() {
    }

    public function showPosition($position){
        // show is given => show matches only 
        if (!empty($this->showJobs) && !in_array($position, $this->showJobs)){
            return FALSE;
        }
        // hide defined jobs, show all others => config: ignoriere_jobs && shortcode: ignoriere_jobs
        if (!empty($this->hideJobs) && in_array($position, $this->hideJobs)){
            return FALSE;
        }
        return TRUE;
    }


        // 2DO:

        // connect to rrze-hub db with readonly-user 

        // personByOrga => group by department
        // personByOrgaPhonebook => group by department
        // personAll => group by work and sort by orga_postion_order

        // exception handling

        // getLectures mit allen Variationen

        // setJobs
        // getJobs


    public function getPerson($aAtts){
        global $wpdb;
        $aRet = [];

        if (!empty($aAtts['person_id'])){
            // get person by its univis ID
            $prepare_vals = [
                $aAtts['person_id']
            ];
            $sClause = "person_id = %s";
        }elseif(!empty($aAtts['name'])){
            // get person by its fullname (= lastname,firstname)
            $parts = explode(',', strtolower($aAtts['name']));
            $prepare_vals = [
                !empty($parts[0]) ? trim($parts[0]) : '',
                !empty($parts[1]) ? trim($parts[1]) : ''
            ];
            $sClause = "LOWER(lastname) = %s AND LOWER(firstname) = %s";
        }elseif(!empty($aAtts['univisID'])){
            // get all persons refering to univisID (=department)
            $prepare_vals = [
                $aAtts['univisID']
            ];
            $this->showJobs = (!empty($this->atts['zeige_jobs']) ? explode('|', $this->atts['zeige_jobs']) : []);
            $this->hideJobs = (!empty($this->atts['ignoriere_jobs']) ? explode('|', $this->atts['ignoriere_jobs']) : []);
        
            // $sClause = "univisID = %s ORDER BY orga_position_order";
            $sClause = "univisID = %s";
        }

        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM getPersons WHERE " . $sClause, $prepare_vals), ARRAY_A);
        if ($wpdb->last_error){
            echo json_encode($wpdb->last_error);
            exit;
        }

        $aLocations = [];
        $aOfficehours = [];

        foreach ($rows as $row) {
            $aRet[$row['ID']] = [
                'person_id' => $row['person_id'],
                'title' => $row['title'],
                'title_long' => $row['title_long'],
                'atitle' => $row['atitle'],
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
                'work' => $row['work'],
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
        }

        // group by  - 2DO: if $aAtts['groupBy'] is a valid field in $aRet => reset($aRet) and check if isset($aRet[0][$aAtts['groupBy']])
        if (!empty($aAtts['groupBy'])){
            $aTmp = [];
            foreach($aRet as $person){
                $aTmp[$person[$aAtts['groupBy']]][] = $person; 
            }
            $aRet = $aTmp;
        }

        return $aRet;
    }
}
