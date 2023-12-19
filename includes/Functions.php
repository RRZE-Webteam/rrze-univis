<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

use function RRZE\UnivIS\Config\getFields;
use RRZE\UnivIS\ICS;


class Functions
{

    protected $pluginFile;

    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    public function onLoaded()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('wp_ajax_GetUnivISData', [$this, 'ajaxGetUnivISData']);
        add_action('wp_ajax_nopriv_GetUnivISData', [$this, 'ajaxGetUnivISData']);
        add_action('wp_ajax_GetUnivISDataForBlockelements', [$this, 'ajaxGetUnivISDataForBlockelements']);
        add_action('wp_ajax_nopriv_GetUnivISDataForBlockelements', [$this, 'ajaxGetUnivISDataForBlockelements']);
        add_action('wp_ajax_GenerateICS', [$this, 'ajaxGenerateICS'] );
        add_action('wp_ajax_nopriv_GenerateICS', [$this, 'ajaxGenerateICS']);
    }

    public static function isInternAllowed($settings)
    {
        $remoteIP = $_SERVER['REMOTE_ADDR'];
        $remoteAdr = gethostbyaddr($remoteIP);

        $options = $settings->options;

        // if user surfs within our network (hosts are defined in settings)
        if (!empty($options['basic_public_visiblity_required_hosts'])) {
            $required_hosts = trim($options['basic_public_visiblity_required_hosts']);
            $aAllowedHosts = preg_split("/[\s,\n]+/", $required_hosts);
            $ret = false;
            foreach ($aAllowedHosts as $host) {
                if ((strpos($remoteAdr, $host) !== false)) {
                    $ret = true;
                    break;
                }
            }

            return $ret;
        }

        return false;
    }

    public function ajaxGenerateICS(){
        check_ajax_referer('univis-ajax-ics-nonce', 'ics_nonce');
        $inputs = filter_input(INPUT_GET, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $aProps = json_decode(openssl_decrypt(base64_decode($inputs['v']), 'AES-256-CBC', hash('sha256', AUTH_KEY), 0, substr(hash('sha256', AUTH_SALT), 0, 16)), true);

        $ics = new ICS($aProps);
        $response = [
            'icsData' => $ics->toString(),
            'filename' => $aProps['FILENAME']
        ];

        wp_send_json($response);
    }

    public function enqueueScripts(){
        wp_enqueue_script(
            'rrze-unvis-ajax-frontend',
            plugins_url('src/js/rrze-univis-frontend.js', plugin_basename($this->pluginFile)),
            ['jquery'],
            null
        );

        wp_localize_script('rrze-unvis-ajax-frontend', 'univis_frontend_ajax', [
            'ajax_frontend_url' => admin_url('admin-ajax.php'),
            'ics_nonce' => wp_create_nonce('univis-ajax-ics-nonce'),
        ]);
    }

    public function adminEnqueueScripts()
    {
        wp_enqueue_script(
            'rrze-unvis-ajax',
            plugins_url('js/rrze-univis.js', plugin_basename($this->pluginFile)),
            ['jquery'],
            null
        );

        wp_localize_script('rrze-unvis-ajax', 'univis_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('univis-ajax-nonce'),
        ]);
    }

    public function getTableHTML($aIn)
    {
        if (!is_array($aIn)) {
            return $aIn;
        }
        $ret = '<table class="wp-list-table widefat striped"><thead><tr><td><b><i>Univ</i>IS</b> ID</td><td><strong>Name</strong></td></tr></thead>';
        foreach ($aIn as $ID => $val) {
            $ret .= "<tr><td>$ID</td><td style='word-wrap: break-word;'>$val</td></tr>";
        }
        $ret .= '</table>';
        return $ret;
    }

    public function ajaxGetUnivISData()
    {
        check_ajax_referer('univis-ajax-nonce', 'nonce');
        $inputs = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $response = $this->getTableHTML($this->getUnivISData(null, $inputs['dataType'], $inputs['keyword']));
        wp_send_json($response);
    }

    public function getSelectHTML($aIn)
    {
        if (!is_array($aIn)) {
            return "<option value=''>$aIn</option>";
        }
        $ret = '<option value="">' . __('-- All --', 'rrze-univis') . '</option>';
        natsort($aIn);
        foreach ($aIn as $ID => $val) {
            $ret .= "<option value='$ID'>$val</option>";
        }
        return $ret;
    }

    public function getUnivISData($univisOrgID = null, $dataType = '', $keyword = null)
    {
        $data = false;
        $ret = __('No matching entries found.', 'rrze-univis'); // Keine passenden Einträge gefunden.

        $options = get_option('rrze-univis');
        $data = 0;
        $UnivISURL = (!empty($options['basic_univis_url']) ? $options['basic_univis_url'] : 'https://univis.uni-erlangen.de');
        $univisOrgID = (!empty($univisOrgID) ? $univisOrgID : (!empty($options['basic_UnivISOrgNr']) ? $options['basic_UnivISOrgNr'] : 0));

        if ($UnivISURL) {
            $univis = new UnivISAPI(null, $UnivISURL, $univisOrgID, null);
            $data = $univis->getData($dataType, $keyword);
        } elseif (!$UnivISURL) {
            $ret = __('Link to UnivIS is missing.', 'rrze-univis');
        }

        if ($data) {
            $ret = [];
            switch ($dataType) {
                case 'departmentByName':
                    foreach ($data as $entry) {
                        if (isset($entry['orgnr'])) {
                            $ret[$entry['orgnr']] = $entry['name'];
                        }
                    }
                    break;
                case 'personByName':
                    foreach ($data as $entry) {
                        if (isset($entry['person_id'])) {
                            $ret[$entry['person_id']] = $entry['lastname'] . ', ' . $entry['firstname'];
                        }
                    }
                    break;
                case 'personAll':
                    foreach ($data as $position => $entries) {
                        foreach ($entries as $entry) {
                            if (isset($entry['person_id'])) {
                                $ret[$entry['person_id']] = $entry['lastname'] . ', ' . $entry['firstname'];
                            }
                        }
                    }
                    break;
                case 'lectureByName':
                    foreach ($data as $entry) {
                        if (isset($entry['lecture_id'])) {
                            $ret[$entry['lecture_id']] = $entry['name'];
                        }
                    }
                    break;
                case 'lectureByDepartment':
                    foreach ($data as $type => $entries) {
                        foreach ($entries as $entry) {
                            if (isset($entry['lecture_id'])) {
                                $ret[$entry['lecture_id']] = $entry['name'];
                            }
                        }
                    }
                    break;
                default:
                    $ret = 'unknown dataType';
                    break;
            }
        }

        return $ret;
    }

    public function ajaxGetUnivISDataForBlockelements()
    {
        check_ajax_referer('univis-ajax-nonce', 'nonce');
        $inputs = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $response = $this->getSelectHTML($this->getUnivISData($inputs['univisOrgID'], $inputs['dataType']));
        wp_send_json($response);
    }

    public static function makeLinkToICS($type, $lecture, $term, $t)
    {
        $aProps = [
            'SUMMARY' => $lecture['title'],
            'LOCATION' => (!empty($t['room']) ? $t['room'] : ''),
            'DESCRIPTION' => (!empty($lecture['comment']) ? $lecture['comment'] . '\n\n' : '') . 'Information: ' . get_permalink() . '\n\n' . (!empty($term['room']['north']) && !empty($term['room']['east']) ? 'Map: https://karte.fau.de/api/v1/iframe/marker/' . $term['room']['north'] . ',' . $term['room']['east'] . '/zoom/16 \n\n' : ''),
            'FILENAME' => sanitize_file_name($type . '.ics'),
        ];

        if (empty($term['startdate']) || empty($term['enddate'])) {
            $thisMonth = date('m');

            if ($thisMonth > 2 && $thisMonth < 8) {
                $sem = 'ss';
            } else {
                $sem = 'ws';
            }

            $options = get_option('rrze-univis');
            $semStart = (!empty($options['basic_' . $sem . 'Start']) ? $options['basic_' . $sem . 'Start'] : null);
            $semEnd = (!empty($options['basic_' . $sem . 'End']) ? $options['basic_' . $sem . 'End'] : null);

            if (empty($semStart) || empty($semEnd)) {
                $defaults = getFields();
               
                foreach ($defaults['basic'] as $nr => $aVal) {  
                    if ((empty($semStart)) && ($aVal['name'] == $sem . 'Start')) {
                        $semStart = $aVal['default'];
                    }
                    if ((empty($semEnd)) && ($aVal['name'] == $sem . 'End')) {
                        $semEnd = $aVal['default'];
                    }
                }

                $semStart = (!empty($semStart) ? $semStart : $defaults['basic'][$sem . 'Start']['default']);
                $semEnd = (!empty($semEnd) ? $semEnd : $defaults['basic'][$sem . 'End']['default']);
            }
        }

        $aFreq = [
            "w1" => 'WEEKLY;INTERVAL=1',
            "w2" => 'WEEKLY;INTERVAL=2',
            "w3" => 'WEEKLY;INTERVAL=3',
            "w4" => 'WEEKLY;INTERVAL=4',
            "m1" => 'MONTHLY;INTERVAL=1',
            "m2" => 'MONTHLY;INTERVAL=2',
            "m3" => 'MONTHLY;INTERVAL=3',
            "m4" => 'MONTHLY;INTERVAL=4',
        ];

        $aWeekdays = [
            '1' => [
                'short' => 'MO',
                'long' => 'Monday',
            ],
            '2' => [
                'short' => 'TU',
                'long' => 'Tuesday',
            ],
            '3' => [
                'short' => 'WE',
                'long' => 'Wednesday',
            ],
            '4' => [
                'short' => 'TH',
                'long' => 'Thursday',
            ],
            '5' => [
                'short' => 'FR',
                'long' => 'Friday',
            ],
        ];

        $aGivenDays = [];

        if (!empty($term['repeatNr'])) {
            $aParts = explode(' ', $term['repeatNr']);
            if (!empty($aFreq[$aParts[0]])) {
                $aProps['FREQ'] = $aFreq[$aParts[0]];
                $aGivenDays = explode(',', $aParts[1]);
                $aProps['REPEAT'] = '';
                foreach ($aWeekdays as $nr => $val) {
                    if (in_array($nr, $aGivenDays)) {
                        $aProps['REPEAT'] .= $val['short'] . ',';
                    }
                }
                $aProps['REPEAT'] = rtrim($aProps['REPEAT'], ',');
            }
        }

        $tStart = (empty($term['starttime']) ? '00:00' : $term['starttime']);
        $tEnd = (empty($term['endtime']) ? '23:59' : $term['endtime']);
        $dStart = (empty($term['startdate']) ? $semStart : $term['startdate']);
        $dEnd = (empty($term['startdate']) ? $semEnd : $term['enddate']);
        $aProps['DTSTART'] = date('Ymd\THis', strtotime(date('Ymd', strtotime($dStart)) . date('Hi', strtotime($tStart))));
        $aProps['DTEND'] = date('Ymd\THis', strtotime(date('Ymd', strtotime($dStart)) . date('Hi', strtotime($tEnd))));
        $aProps['UNTIL'] = date('Ymd\THis', strtotime(date('Ymd', strtotime($dEnd)) . date('Hi', strtotime($tEnd))));

        if (!empty($aGivenDays)) {
            // check if day of week of DTSTART is a member of the REPEAT days
            $givenWeekday = date('N', strtotime($aProps['DTSTART']));
            if (!in_array($givenWeekday, $aGivenDays)) {
                // move to next possible date
                while(!in_array($givenWeekday, $aGivenDays)){
                    $givenWeekday++;
                    $givenWeekday = ($givenWeekday > 5 ? 1 : $givenWeekday);
                    if (in_array($givenWeekday, $aGivenDays)){
                        $aProps['DTSTART'] = date('Ymd', strtotime("next " . $aWeekdays[$givenWeekday]['long'], strtotime($aProps['DTSTART'])));
                        $aProps['DTEND'] = $aProps['DTSTART'] . date('\THis', strtotime($tEnd));
                        $aProps['DTSTART'] .= date('\THis', strtotime($tStart));
                        break;
                    }
                }
            }
        }

        $propsEncoded = base64_encode(openssl_encrypt(json_encode($aProps), 'AES-256-CBC', hash('sha256', AUTH_KEY), 0, substr(hash('sha256', AUTH_SALT), 0, 16)));

        $linkParams = [
            'v' => $propsEncoded,
            'h' => hash('sha256', $propsEncoded),
        ];

        return [
            'link' => http_build_query($linkParams),
            'linkTxt' => __('ICS', 'rrze-univis') . ': ' . __('Date', 'rrze-univis') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('import to calendar', 'rrze-univis'),
        ];
    }

}
