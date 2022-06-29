<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Functions
{

    protected $pluginFile;

    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    public function onLoaded()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('wp_ajax_GetUnivISData', [$this, 'ajaxGetUnivISData']);
        add_action('wp_ajax_nopriv_GetUnivISData', [$this, 'ajaxGetUnivISData']);
        add_action('wp_ajax_GetUnivISDataForBlockelements', [$this, 'ajaxGetUnivISDataForBlockelements']);
        add_action('wp_ajax_nopriv_GetUnivISDataForBlockelements', [$this, 'ajaxGetUnivISDataForBlockelements']);
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
            $univis = new UnivISAPI($UnivISURL, $univisOrgID, null);
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

    public static function makeLinkToICS($type, $lecture, $term, $t){
        $options = get_option('rrze-univis');
        $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
        $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
        $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
        $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

        $props = [
            'summary' => $lecture['title'],
            'startdate' => (!empty($term['startdate']) ? $term['startdate'] : null),
            'enddate' => (!empty($term['enddate']) ? $term['enddate'] : null),
            'starttime' => (!empty($term['starttime']) ? $term['starttime'] : null),
            'endtime' => (!empty($term['endtime']) ? $term['endtime'] : null),
            'repeatNr' => (!empty($term['repeatNr']) ? $term['repeatNr'] : null),
            'location' => (!empty($t['room']) ? $t['room'] : null),
            'description' => (!empty($lecture['comment']) ? $lecture['comment'] : null),
            'url' => get_permalink(),
            'map' => (!empty($term['room']['north']) && !empty($term['room']['east']) ? 'https://karte.fau.de/api/v1/iframe/marker/' . $term['room']['north'] . ',' . $term['room']['east'] . '/zoom/16' : ''),
            'filename' => sanitize_file_name($type),
            'ssstart' => $ssstart,
            'ssend' => $ssend,
            'wsstart' => $wsstart,
            'wsend' => $wsend,
        ];

        $propsEncoded = base64_encode(openssl_encrypt(json_encode($props), 'AES-256-CBC', hash('sha256', AUTH_KEY), 0, substr(hash('sha256', AUTH_SALT), 0, 16)));
        $linkParams = [
            'v' => $propsEncoded,
            'h' => hash('sha256', $propsEncoded),
        ];

        $screenReaderTxt = __('ICS', 'rrze-univis') . ': ' . __('Date', 'rrze-univis') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('import to calendar', 'rrze-univis');

        return [
            'link' => wp_nonce_url(plugin_dir_url(__DIR__) . 'ics.php?' . http_build_query($linkParams), 'createICS', 'ics_nonce'),
            'linkTxt' => __('ICS', 'rrze-univis') . ': ' . __('Date', 'rrze-univis') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('import to calendar', 'rrze-univis'),
        ];
    }



}
