<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;
use function RRZE\UnivIS\Config\getShortcodeSettings;


/**
 * Shortcode
 */
class Shortcode{
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;
    protected $UnivISOrgNr;
    protected $UnivISURL;
    protected $UnivISLink;
    protected $show = [];
    protected $hide = [];

    /**
     * Settings-Objekt
     * @var object
     */
    private $settings = '';

    /** 
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    public function __construct($pluginFile, $settings){
        $this->pluginFile = $pluginFile;
        $this->settings = getShortcodeSettings();
        $options = get_option( 'rrze-univis' );
        $this->UnivISOrgNr = (!empty($options['basic_UnivISOrgNr']) ? $options['basic_UnivISOrgNr'] : 0);
        $this->UnivISURL = (!empty($options['basic_univis_url']) ? $options['basic_univis_url'] : '');
        $this->UnivISLink = sprintf('<a href="%1$s">%2$s</a>', $this->UnivISURL, (!empty($options['basic_univis_linktxt']) ? $options['basic_univis_linktxt'] : __('Text zum UnivIS Link fehlt', 'rrze-univis')));
        add_action( 'admin_enqueue_scripts', [$this, 'enqueueGutenberg'] );
        add_action( 'init',  [$this, 'initGutenberg'] );
    }

    /**
     * Er wird ausgeführt, sobald die Klasse instanziiert wird.
     * @return void
     */
    public function onLoaded(){
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('univis', [$this, 'shortcodeOutput'], 10, 2);
    }

    /**
     * Enqueue der Skripte.
     */
    public function enqueueScripts(){
        // wp_register_style('rrze-univis-shortcode', plugins_url('assets/css/shortcode.css', plugin_basename($this->pluginFile)));
    }


    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput( $atts ) {
        if (empty($atts)){
            return $this->UnivISLink;
        }elseif(empty($this->UnivISOrgNr)){
            return __('UnivIS-OrgNr. muss entweder in wp-admin/options-general.php?page=rrze-univis gesetzt oder im Shortcode übergeben werden.', 'rrze-univis');
        }

        // lv_id is not in config (=> id)
        if (!empty($atts['lv_id'])){
            $atts['id'] = (int)$atts['lv_id'];
            if ($atts['task'] == 'lehrveranstaltungen-alle'){
                $atts['task'] = 'lehrveranstaltungen-einzeln';
            }
        }

        // get settings
        switch($atts['task']){
            case 'mitarbeiter-einzeln': 
            case 'mitarbeiter-orga': 
            case 'mitarbeiter-telefonbuch': 
            case 'mitarbeiter-alle': 
                $this->settings = $this->settings['mitarbeiter'];
                break;
            case 'lehrveranstaltungen-einzeln': 
            case 'lehrveranstaltungen-alle': 
                $this->settings = $this->settings['lehrveranstaltungen'];
                break;
            case 'publikationen': 
                $this->settings = $this->settings['publikationen'];
                break;
        }        

        // merge given attributes with default ones
        $atts_default = array();
        foreach( $this->settings as $k => $v ){
            if ( $k != 'block' ){
                $atts_default[$k] = $v['default'];
            }
        }
        $atts = shortcode_atts( $atts_default, $atts );
        $atts = $this->normalize($atts);

        $data = '';
        $univis = new UnivISAPI($this->UnivISURL, $this->UnivISOrgNr, $atts);

        switch($atts['task']){
            case 'mitarbeiter-einzeln': 
                if (!in_array('telefon', $this->hide) && !in_array('telefon', $this->show)){
                    $this->show[] = 'telefon';
                }
                if (!in_array('mail', $this->hide) && !in_array('mail', $this->show)){
                    $this->show[] = 'mail';
                }
                if (!empty($atts['univisid'])){
                    $data = $univis->getData('personByID', $atts['univisid']);
                    if ($data){
                        $atts['name'] = $data[0]['lastname'] . ',' . $data[0]['firstname'];
                    }
                }elseif(!empty($atts['name'])){
                    $data = $univis->getData('personByName', $atts['name']);
                }
                if ($data && !empty($atts['name'])){
                    $person = $data[0];
                    $person['lectures'] = $univis->getData('lectureByLecturer', $atts['name']);
                }
                break;
            case 'mitarbeiter-orga': 
                $data = $univis->getData('personByOrga');
                break;
            case 'mitarbeiter-telefonbuch': 
                $data = $univis->getData('personByOrgaPhonebook');
                break;
            case 'mitarbeiter-alle': 
                if (!in_array('telefon', $this->hide) && !in_array('telefon', $this->show)){
                    $this->show[] = 'telefon';
                }
                $data = $univis->getData('personAll', NULL);
                break;
            case 'lehrveranstaltungen-einzeln': 
                if (!empty($atts['id'])){
                    $data = $univis->getData('lectureByID', $atts['id']);
                }elseif (!empty($atts['name'])){
                    $data = $univis->getData('lectureByLecturer', $atts['name']);
                }elseif (!empty($atts['univisid'])){
                    $data = $univis->getData('lectureByLecturerID', $atts['univisid']);
                }
                if ($data){
                    $veranstaltung = $data[array_key_first($data)][0];
                }
                break;
            case 'lehrveranstaltungen-alle': 
                if (!empty($atts['name'])){
                    $data = $univis->getData('lectureByLecturer', $atts['name']);
                }elseif (!empty($atts['univisid'])){
                    $data = $univis->getData('lectureByLecturerID', $atts['univisid']);
                }else{
                    $data = $univis->getData('lectureByDepartment');
                }
                break;
            case 'publikationen': 
                if (!empty($atts['name'])){
                    $data = $univis->getData('publicationByAuthor', $atts['name']);
                }elseif (!empty($atts['univisid'])){
                    $data = $univis->getData('publicationByAuthorID', $atts['univisid']);
                }else{
                    $data = $univis->getData('publicationByDepartment');
                }
                break;
        }

        if ($data){
            // $data = '<pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';
            // var_dump($data);
            // exit;
            
            $filename = trailingslashit(dirname(__FILE__)) . '../templates/' . $atts['task'] . '.php';
            
            if (is_file($filename)) {
                ob_start();
                include $filename;
                return str_replace("\n", " ", ob_get_clean());
            }
        }else{
            return __('Keine passenden Datensätze gefunden.', 'rrze-univis');
        }
    }

    public function normalize($atts){
        // normalize given attributes according to rrze-univis version 2
        if (empty($atts['task'])){
            $atts['task'] = 'mitarbeiter-orga';
        }
        if (!empty($atts['number'])){
            $this->UnivISOrgNr = (int)$atts['number'];
        }elseif (!empty($atts['task']) && ($atts['task'] == 'lehrveranstaltungen-alle' || $atts['task'] == 'mitarbeiter-einzeln') && !empty($atts['id'])){
            $this->UnivISOrgNr = (int)$atts['id'];
        }
        if (empty($this->UnivISOrgNr)){
            return 'no UnivISOrgNr given';
        }
        if (!empty($atts['dozentid'])){
            $atts['id'] = (int)$atts['dozentid'];
        }
        if (!empty($atts['dozentname'])){
            $atts['name'] = $atts['dozentname'];
        }
        if (!empty($atts['name'])){
            $atts['name'] = str_replace(' ', '', $atts['name']);
        }
        if (empty($atts['show'])){
            $atts['show'] = '';
        }
        if (empty($atts['hide'])){
            $atts['hide'] = '';
        }
        if (isset($atts['show_phone'])){
            if ($atts['show_phone']){
                $atts['show'] .= ',telefon';
            }else{
                $atts['hide'] .= ',telefon';
            }
        }
        if (isset($atts['show_mail'])){
            if ($atts['show_mail']){
                $atts['show'] .= ',mail';
            }else{
                $atts['hide'] .= ',mail';
            }
        }
        if (isset($atts['show_jumpmarks'])){
            if ($atts['show_jumpmarks']){
                $atts['show'] .= ',sprungmarken';
            }else{
                $atts['hide'] .= ',sprungmarken';
            }
        }
        if (!empty($atts['show'])){
            $this->show = array_map('trim', explode(',', strtolower($atts['show'])));
        }
        if (!empty($atts['hide'])){
            $this->hide = array_map('trim', explode(',', strtolower($atts['hide'])));
        }

        return $atts;
    }

    public function isGutenberg(){
        $postID = get_the_ID();
        if ($postID && !use_block_editor_for_post($postID)){
            return false;
        }

        return true;        
    }

    private function makeDropdown($label, $all = NULL){
        return [
            'label' => $label,
            'field_type' => 'select',
            'default' => '',
            'type' => 'string',
            'items' => ['type' => 'text'],
            'values' => [['id' => '', 'val' => (empty($all)?__( '-- Alle --', 'rrze-univis' ):$all)]],
        ];
    }

    private function makeToggle($label){
        return [
            'label' => $label,
            'field_type' => 'toggle',
            'default' => TRUE,
            'checked' => TRUE,
            'type' => 'boolean',
        ];
    }

    public function fillGutenbergOptions() {
        $univis = new UnivISAPI($this->UnivISURL, $this->UnivISOrgNr, NULL);

        foreach($this->settings as $task => $settings){
            $settings['number']['default'] = $this->UnivISOrgNr;

            // Mitarbeiter
            if (isset($settings['name'])){
                unset($settings['name']);
                if ($task != 'lehrveranstaltungen'){
                    unset($settings['id']);
                }
                $aPersons = [];
                $zeige_jobs = (isset($settings['zeige_jobs'])?$settings['zeige_jobs']:NULL);
                $ignoriere_jobs = (isset($settings['ignoriere_jobs'])?$settings['ignoriere_jobs']:NULL);
                $data = $univis->getData('personAll', NULL, 1, $zeige_jobs, $ignoriere_jobs);
                foreach($data as $position => $persons){
                    foreach($persons as $person){
                        $aPersons[$person['person_id']] = $person['lastname'] . ', ' . $person['firstname'];
                    }
                }
                asort($aPersons);            
                $settings['univisid'] = $this->makeDropdown(__('Person', 'rrze-univis'));
                foreach($aPersons as $id => $name){
                    $settings['univisid']['values'][] = [
                        'id' => $id,
                        'val' => $name
                    ];
                }
            }

            // Lehrveranstaltungen
            if (isset($settings['id'])){
                $aLectures = [];
                $aLectureTypes = [];
                $aLectureLanguages = [];
                $data = $univis->getData('lectureByDepartment');

                foreach($data as $type => $lecs){
                    foreach($lecs as $lecture){
                        $aLectureTypes[$lecture['lecture_type']] = $type;
                        if (!empty($lecture['leclanguage'])){
                            $parts = explode(' ', $lecture['leclanguage_long']);
                            $aLectureLanguages[$lecture['leclanguage']] = $parts[1];
                        }
                        $aLectures[$lecture['lecture_id']] = $lecture['name'];
                    }
                }
                asort($aLectures);            
                $settings['id'] = $this->makeDropdown(__('Lehrveranstaltung', 'rrze-univis'));

                foreach($aLectures as $id => $name){
                    $settings['id']['values'][] = [
                        'id' => $id,
                        'val' => $name
                    ];
                }

                asort($aLectureTypes);            
                $settings['type'] = $this->makeDropdown(__('Typ', 'rrze-univis'));

                foreach($aLectureTypes as $id => $name){
                    $settings['type']['values'][] = [
                        'id' => $id,
                        'val' => $name
                    ];
                }

                asort($aLectureLanguages);            
                $settings['sprache'] = $this->makeDropdown(__('Sprache', 'rrze-univis'));

                foreach($aLectureLanguages as $id => $name){
                    $settings['sprache']['values'][] = [
                        'id' => $id,
                        'val' => $name
                    ];
                }

                // Semester
                if (isset($settings['sem'])){
                    $settings['sem'] = $this->makeDropdown(__('Sprache', 'rrze-univis'), __( '-- Aktuelles Semester --', 'rrze-univis' ));
                    $lastYear = date("Y") - 1;

                    for ($i = $lastYear; $i > 2000; $i--){
                        $settings['sem']['values'][] = [
                            'id' => $id . 's',
                            'val' => $i . ' ' . __( 'SS', 'rrze-univis' )
                        ];
                        $settings['sem']['values'][] = [
                            'id' => $id . 'w',
                            'val' => $i . ' ' . __( 'WS', 'rrze-univis' )
                        ];
                    }
                }
            }

            // show/hide 
            if (isset($settings['show'])){
                unset($settings['show']);
                unset($settings['hide']);
                $settings['show_phone'] = $this->makeToggle(__( 'Telefonnummern anzeigen', 'rrze-univis' ));
                $settings['show_mail'] = $this->makeToggle(__( 'eMail anzeigen', 'rrze-univis' ));
                $settings['show_jumpmarks'] = $this->makeToggle(__( 'Sprungmarken anzeigen', 'rrze-univis' ));
            }

            // 2DO: we need document ready() or equal on React built elements to use onChange of UnivIS Org Nr. to refill dropdowns 
            unset($settings['number']);

            $this->settings[$task] = $settings;
        }
        return $this->settings;
    }


    public function initGutenberg() {
        if (! $this->isGutenberg() || empty($this->UnivISURL) || empty($this->UnivISOrgNr)){
            return;
        }

        // get prefills for dropdowns
        $this->settings = $this->fillGutenbergOptions();

        foreach($this->settings as $task => $settings){
            // register js-script to inject php config to call gutenberg lib
            $editor_script = $settings['block']['blockname'] . '-block';        
            $js = '../src/js/' . $editor_script . '.js';

            wp_register_script(
                $editor_script,
                plugins_url( $js, __FILE__ ),
                array(
                    'RRZE-Gutenberg',
                ),
                NULL
            );
            $settings['test'] = 'testit';
            wp_localize_script( $editor_script, $settings['block']['blockname'] . 'Config', $settings );

            // register block
            register_block_type( $settings['block']['blocktype'], array(
                'editor_script' => $editor_script,
                'render_callback' => [$this, 'shortcodeOutput'],
                'attributes' => $settings
                ) 
            );
        }
    }

    public function enqueueGutenberg(){
        if (! $this->isGutenberg()){
            return;
        }

        // include gutenberg lib
        wp_enqueue_script(
            'RRZE-Gutenberg',
            plugins_url( '../assets/js/gutenberg.js', __FILE__ ),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor'
            ),
            NULL
        );
    }
}
