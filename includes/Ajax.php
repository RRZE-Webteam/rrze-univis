<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Ajax {
    protected $plugin;
    protected $config;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $this->config = new Config();
    }

    public function onLoaded(): void {
        $constants = $this->config->getConstants();

        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('wp_ajax_' . $constants['ajax']['search_action'], [$this, 'ajaxGetUnivISData']);
        add_action('wp_ajax_nopriv_' . $constants['ajax']['search_action'], [$this, 'ajaxGetUnivISData']);
        add_action('wp_ajax_' . $constants['ajax']['block_elements_action'], [$this, 'ajaxGetUnivISDataForBlockelements']);
        add_action('wp_ajax_nopriv_' . $constants['ajax']['block_elements_action'], [$this, 'ajaxGetUnivISDataForBlockelements']);
    }

    public function adminEnqueueScripts(): void {
        $constants = $this->config->getConstants();

        wp_enqueue_script(
            $constants['ajax']['admin_script_handle'],
            $this->plugin->getUrl($constants['ajax']['admin_script_path']),
            ['jquery'],
            null
        );

        wp_localize_script($constants['ajax']['admin_script_handle'], $constants['ajax']['admin_script_object'], [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce($constants['ajax']['nonce_action']),
        ]);
    }

    public function getTableHTML(mixed $aIn): mixed {
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

    public function ajaxGetUnivISData(): void {
        $constants = $this->config->getConstants();
        check_ajax_referer($constants['ajax']['nonce_action'], $constants['ajax']['nonce_name']);
        $inputs = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $response = $this->getTableHTML($this->getUnivISData(null, $inputs['dataType'], $inputs['keyword']));
        wp_send_json($response);
    }

    public function getSelectHTML(mixed $aIn): string {
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

    public function getUnivISData(mixed $univisOrgID = null, string $dataType = '', ?string $keyword = null): mixed {
        $data = false;
        $ret = __('No matching entries found.', 'rrze-univis'); // Keine passenden Einträge gefunden.

        $options = get_option('rrze-univis');
        $constants = $this->config->getConstants();
        $data = 0;
        $UnivISURL = (!empty($options['basic_univis_url']) ? $options['basic_univis_url'] : $constants['defaults']['univis_url']);
        $univisOrgID = (!empty($univisOrgID) ? $univisOrgID : (!empty($options['basic_UnivISOrgNr']) ? $options['basic_UnivISOrgNr'] : 0));

        if ($UnivISURL) {
            $univis = new Cache($UnivISURL, $univisOrgID, null);
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

    public function ajaxGetUnivISDataForBlockelements(): void {
        $constants = $this->config->getConstants();
        check_ajax_referer($constants['ajax']['nonce_action'], $constants['ajax']['nonce_name']);
        $inputs = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $response = $this->getSelectHTML($this->getUnivISData($inputs['univisOrgID'], $inputs['dataType']));
        wp_send_json($response);
    }
}
