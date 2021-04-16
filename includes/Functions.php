<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Functions {

    protected $pluginFile;


    public function __construct($pluginFile){
        $this->pluginFile = $pluginFile;
    }

    public function onLoaded(){
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('wp_ajax_GetUnivISData', [$this, 'ajaxGetUnivISData']);
        add_action('wp_ajax_nopriv_GetUnivISData', [$this, 'ajaxGetUnivISData']);
    }

    public function adminEnqueueScripts(){
        wp_enqueue_script(
			'rrze-unvis-ajax',
			plugins_url('src/js/univis.js', plugin_basename($this->pluginFile)),
			['jquery'],
			NULL
        );    

        wp_localize_script('rrze-unvis-ajax', 'univis_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'univis-ajax-nonce' ),
        ]);

    }


    public function getUnivISDataHTML($keyword, $dataType){
        $data = FALSE;
        $ret = __('Keine passenden Einträge gefunden.', 'rrze-univis');

        if ($keyword){
            $options = get_option( 'rrze-univis' );
            $data = 0;
            $UnivISURL = (!empty($options['basic_univis_url']) ? $options['basic_univis_url'] : '');
            $univisOrgID = (!empty($options['basic_UnivISOrgNr']) ? $options['basic_UnivISOrgNr'] : 0);

            if ($UnivISURL){
                $univis = new UnivISAPI($UnivISURL, $univisOrgID, NULL);
                $data = $univis->getData($dataType, $keyword);
            }elseif (!$UnivISURL){
                $ret =  __('Link zu UnivIS fehlt.', 'rrze-univis');
            }
        }

        if ($data){
            $ret = '<table class="wp-list-table widefat striped"><thead><tr><td><b><i>Univ</i>IS</b> ID</td><td><strong>Name</strong></td></tr></thead>';
            switch ($dataType){
                case 'departmentByName':
                    foreach($data as $entry){
                        if (isset($entry['orgnr'])){
                            $ret .= '<tr><td>' . $entry['orgnr'] . '</td><td style="word-wrap: break-word;">' . $entry['name'] . '</td></tr>';
                        }
                    }
                    break;
                case 'personByName':
                    foreach($data as $entry){
                        if (isset($entry['person_id'])){
                            $ret .= '<tr><td>' . $entry['person_id'] . '</td><td style="word-wrap: break-word;">' . $entry['lastname'] . ', ' . $entry['firstname'] . '</td></tr>';
                        }
                    }
                    break;
                case 'lectureByName':
                    foreach($data as $entry){
                        if (isset($entry['lecture_id'])){
                            $ret .= '<tr><td>' . $entry['lecture_id'] . '</td><td style="word-wrap: break-word;">' . $entry['name'] . '</td></tr>';
                        }
                    }
                    break;
                default:
                $ret .= '<tr><td colspan="2">unknown dataType</td></tr>';
                    break;
            }
            $ret .= '</table>';
        }

        return $ret;
    }

    public function ajaxGetUnivISData() {
        check_ajax_referer( 'univis-ajax-nonce', 'nonce'  );
        $inputs = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $response = $this->getUnivISDataHTML($inputs['keyword'], $inputs['dataType']);
        wp_send_json($response);
    }

}
