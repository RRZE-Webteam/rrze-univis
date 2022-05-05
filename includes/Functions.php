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
        $ret = __('Keine passenden Einträge gefunden.', 'rrze-univis');

        $options = get_option('rrze-univis');
        $data = 0;
        $UnivISURL = (!empty($options['basic_univis_url']) ? $options['basic_univis_url'] : 'https://univis.uni-erlangen.de');
        $univisOrgID = (!empty($univisOrgID) ? $univisOrgID : (!empty($options['basic_UnivISOrgNr']) ? $options['basic_UnivISOrgNr'] : 0));

        if ($UnivISURL) {
            $univis = new UnivISAPI($UnivISURL, $univisOrgID, null);
            $data = $univis->getData($dataType, $keyword);
        } elseif (!$UnivISURL) {
            $ret = __('Link zu UnivIS fehlt.', 'rrze-univis');
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

}
