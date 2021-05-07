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
    protected $options;
    protected $show = [];
    protected $hide = [];
    protected $atts;
    protected $univis;
    protected $noCache = FALSE;
    const TRANSIENT_PREFIX = 'rrze_univis_cache_';    
    const TRANSIENT_EXPIRATION = DAY_IN_SECONDS;    

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
        $this->options = get_option( 'rrze-univis' );
        $this->UnivISOrgNr = (!empty($this->options['basic_UnivISOrgNr']) ? $this->options['basic_UnivISOrgNr'] : 0);
        $this->UnivISURL = (!empty($this->options['basic_univis_url']) ? $this->options['basic_univis_url'] : '');
        $this->UnivISLink = sprintf('<a href="%1$s">%2$s</a>', $this->UnivISURL, (!empty($this->options['basic_univis_linktxt']) ? $this->options['basic_univis_linktxt'] : __('Text zum UnivIS Link fehlt', 'rrze-univis')));
        add_action( 'admin_enqueue_scripts', [$this, 'enqueueGutenberg'] );
        add_action( 'init',  [$this, 'initGutenberg'] );
        add_action( 'enqueue_block_assets', [$this, 'enqueueBlockAssets'] );
    }

    /**
     * Er wird ausgeführt, sobald die Klasse instanziiert wird.
     * @return void
     */
    public function onLoaded(){
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('univis', [$this, 'shortcodeOutput'], 10, 2);
    }

    public function enqueueScripts(){
        wp_register_style('rrze-univis', plugins_url('css/rrze-univis.css', plugin_basename($this->pluginFile)));
        wp_enqueue_style( 'rrze-univis' );
    }


    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput( $atts ) {
        $this->settings = getShortcodeSettings();

        if (empty($atts)){
            return $this->UnivISLink;
        }

        if (!empty($atts['nocache'])){
            $this->noCache = TRUE;
        }

        // lv_id is not in config (=> id)
        if (!empty($atts['lv_id'])){
            $atts['id'] = $atts['lv_id'];
            if ($atts['task'] == 'lehrveranstaltungen-alle'){
                $atts['task'] = 'lehrveranstaltungen-einzeln';
            }
        }

        if (empty($atts['task'])){
            $atts['task'] = 'mitarbeiter-alle';
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

        $this->atts = $this->normalize(shortcode_atts($atts_default, $atts));

        $data = '';
        $this->univis = new UnivISAPI($this->UnivISURL, $this->UnivISOrgNr, $this->atts);

        switch($this->atts['task']){
            case 'mitarbeiter-einzeln': 
                if (!in_array('telefon', $this->hide) && !in_array('telefon', $this->show)){
                    $this->show[] = 'telefon';
                }
                if (!in_array('mail', $this->hide) && !in_array('mail', $this->show)){
                    $this->show[] = 'mail';
                }
                if (!empty($atts['univisid'])){
                    $data = $this->getData('personByID', $this->atts['univisid']);
                    if ($data){
                        $this->atts['name'] = $data[0]['lastname'] . ',' . $data[0]['firstname'];
                    }
                }elseif(!empty($this->atts['name'])){
                        $data = $this->getData('personByName', $this->atts['name']);
                }
                if ($data && !empty($this->atts['name'])){
                    $person = $data[0];
                    $person['lectures'] = $this->getData('lectureByLecturer', $this->atts['name']);
                }
                break;
            case 'mitarbeiter-orga': 
                $data = $this->getData('personByOrga');
                break;
            case 'mitarbeiter-telefonbuch': 
                $data = $this->getData('personByOrgaPhonebook');
                break;
            case 'mitarbeiter-alle': 
                if (!in_array('telefon', $this->hide) && !in_array('telefon', $this->show)){
                    $this->show[] = 'telefon';
                }
                $data = $this->getData('personAll', NULL);
                break;
            case 'lehrveranstaltungen-einzeln': 
                if (!empty($this->atts['id'])){
                    $data = $this->getData('lectureByID', $this->atts['id']);
                }elseif (!empty($this->atts['name'])){
                    $data = $this->getData('lectureByLecturer', $this->atts['name']);
                }elseif (!empty($this->atts['univisid'])){
                    $data = $this->getData('lectureByLecturerID', $this->atts['univisid']);
                }elseif (!empty($this->atts['id'])){
                    $data = $this->getData('lectureByLecturerID', $this->atts['id']);
                }
                if ($data){
                    $veranstaltung = $data[array_key_first($data)][0];
                }
                break;
            case 'lehrveranstaltungen-alle': 
                if (!empty($this->atts['name'])){
                    $data = $this->getData('lectureByLecturer', $this->atts['name']);
                }elseif (!empty($this->atts['univisid'])){
                    $data = $this->getData('lectureByLecturerID', $this->atts['univisid']);
                }elseif (!empty($this->atts['id'])){
                    $data = $this->getData('lectureByLecturerID', $this->atts['id']);
                }else{
                    $data = $this->getData('lectureByDepartment');
                }
                break;
            case 'publikationen': 
                if (!empty($atts['name'])){
                    $data = $this->getData('publicationByAuthor', $this->atts['name']);
                }elseif (!empty($this->atts['univisid'])){
                    $data = $this->getData('publicationByAuthorID', $this->atts['univisid']);
                }else{
                    $data = $this->getData('publicationByDepartment');
                }
                break;
        }

        if ($data && is_array($data)){
            // $data = '<pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';
            // var_dump($data);
            // exit;
            
            $filename = trailingslashit(dirname(__FILE__)) . '../templates/' . $this->atts['task'] . '.php';
            
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
        if (!empty($atts['number'])){
            $this->UnivISOrgNr = $atts['number'];
        }elseif (!empty($atts['task']) && ($atts['task'] == 'lehrveranstaltungen-alle' || $atts['task'] == 'mitarbeiter-einzeln') && !empty($atts['id'])){
            $this->UnivISOrgNr = $atts['id'];
        }
        if (!empty($atts['dozentid'])){
            $atts['id'] = $atts['dozentid'];
        }
        if (!empty($atts['dozentname'])){
            $atts['name'] = $atts['dozentname'];
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
        if (isset($atts['ics'])){
            if ($atts['ics']){
                $atts['show'] .= ',ics';
            }else{
                $atts['hide'] .= ',ics';
            }
        }
        if (isset($atts['call'])){
            if ($atts['call']){
                $atts['show'] .= ',call';
            }else{
                $atts['hide'] .= ',call';
            }
        }
        if (!empty($atts['show'])){
            $this->show = array_map('trim', explode(',', strtolower($atts['show'])));
        }
        if (!empty($atts['hide'])){
            $this->hide = array_map('trim', explode(',', strtolower($atts['hide'])));
        }
        if (!empty($atts['sem'])){
            if (is_int($atts['sem'])){
                $year = date("Y") + $atts['sem'];
                $thisSeason = (in_array(date('n'), [10,11,12,1]) ? 'w' : 's');
                $season = ($thisSeason = 's' ? 'w' : 's');
                $atts['sem'] = $year . $season;
            }
        }
        if (empty($atts['hstart'])){
            $atts['hstart'] = $this->options['basic_hstart'];
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

    private function makeDropdown($id, $label, $aData, $all = NULL){
        $ret = [
            'id' => $id,
            'label' => $label,
            'field_type' => 'select',
            'default' => '',
            'type' => 'string',
            'items' => ['type' => 'text'],
            'values' => [['id' => '', 'val' => (empty($all)?__( '-- Alle --', 'rrze-univis' ):$all)]]
        ];

        foreach($aData as $id => $name){
            $ret['values'][] = [
                'id' => $id,
                'val' => htmlspecialchars(str_replace('"', "", str_replace("'", "", $name)), ENT_QUOTES, 'UTF-8')
            ];
        }

        return $ret;
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

    public function fillGutenbergOptions($aSettings) {
        $this->univis = new UnivISAPI($this->UnivISURL, $this->UnivISOrgNr, NULL);

        foreach($aSettings as $task => $settings){
            $settings['number']['default'] = $this->UnivISOrgNr;

            // Mitarbeiter
            if (isset($settings['name'])){
                unset($settings['name']);
                if ($task != 'lehrveranstaltungen'){
                    unset($settings['id']);
                }
                $aPersons = [];
                $data = $this->getData('personAll');
                foreach($data as $position => $persons){
                    foreach($persons as $person){
                        $aPersons[$person['person_id']] = $person['lastname'] . (!empty($person['firstname']) ? ', ' . $person['firstname'] : '');
                    }
                }
                asort($aPersons);            
                $settings['univisid'] = $this->makeDropdown('univisid', __('Person', 'rrze-univis'), $aPersons);

            }

            // Lehrveranstaltungen
            if (isset($settings['id'])){
                $aLectures = [];
                $aLectureTypes = [];
                $aLectureLanguages = [];
                $data = $this->getData('lectureByDepartment');

                foreach($data as $type => $lecs){
                    foreach($lecs as $lecture){
                        $aLectureTypes[$lecture['lecture_type']] = $type;
                        if (!empty($lecture['leclanguage_long'])){
                            $parts = explode(' ', $lecture['leclanguage_long']);
                            $aLectureLanguages[$lecture['leclanguage']] = $parts[1];
                        }
                        $aLectures[$lecture['lecture_id']] = $lecture['name'];
                    }
                }
                
                asort($aLectures);            
                $settings['id'] = $this->makeDropdown('id', __('Lehrveranstaltung', 'rrze-univis'), $aLectures);

                asort($aLectureTypes);            
                $settings['type'] = $this->makeDropdown('type', __('Typ', 'rrze-univis'), $aLectureTypes);

                asort($aLectureLanguages);            
                $settings['sprache'] = $this->makeDropdown('sprache', __('Sprache', 'rrze-univis'), $aLectureLanguages);

                // Semester
                if (isset($settings['sem'])){
                    $settings['sem'] = $this->makeDropdown('sem', __('Semester', 'rrze-univis'), [], __( '-- Aktuelles Semester --', 'rrze-univis' ));
                    $thisSeason = (in_array(date('n'), [10,11,12,1]) ? 'w' : 's');
                    $season = ($thisSeason = 's' ? 'w' : 's');
                    $nextYear = date("Y") + 1;
                    $settings['sem']['values'][] = ['id' => $nextYear.$season, 'val' => $nextYear.$season];
                    $lastYear = $nextYear - 2;
                    $settings['sem']['values'][] = ['id' => $lastYear.$season, 'val' => $lastYear.$season];

                    $minYear = (!empty($this->options['basic_semesterMin']) ? $this->options['basic_semesterMin'] : 1971);
                    for ($i = date("Y"); $i >= $minYear; $i--){
                        $settings['sem']['values'][] = ['id' => $i . 's', 'val' => $i . ' ' . __( 'SS', 'rrze-univis' )];
                        $settings['sem']['values'][] = ['id' => $i . 'w', 'val' => $i . ' ' . __( 'WS', 'rrze-univis' )];
                    }
                }
            }

            // 2DO: we need document ready() or equal on React built elements to use onChange of UnivIS Org Nr. to refill dropdowns 
            // unset($settings['number']);
            unset($settings['show']);
            unset($settings['hide']);

            $aSettings[$task] = $settings;
        }
        return $aSettings;
    }


    public function initGutenberg() {
        if (! $this->isGutenberg() || empty($this->UnivISURL) ){
            return;
        }
        // get prefills for dropdowns
        $aSettings = $this->fillGutenbergOptions($this->settings);

        foreach($aSettings as $task => $settings){
            // register js-script to inject php config to call gutenberg lib
            $editor_script = $settings['block']['blockname'] . '-block';        
            $js = '../js/' . $editor_script . '.js';

            wp_register_script(
                $editor_script,
                plugins_url( $js, __FILE__ ),
                array(
                    'RRZE-Gutenberg',
                ),
                NULL
            );

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

        wp_dequeue_script('RRZE-Gutenberg');
        // include gutenberg lib
        wp_enqueue_script(
            'RRZE-Gutenberg',
            plugins_url( '../src/js/gutenberg.js', __FILE__ ),
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

    public function enqueueBlockAssets(){
        wp_dequeue_script('RRZE-UnivIS-BlockJS');
        // include blockeditor JS
        wp_enqueue_script(
            'RRZE-UnivIS-BlockJS',
            plugins_url( '../src/js/rrze-univis-blockeditor.js', __FILE__ ),
            array(
                'jquery',
                'RRZE-Gutenberg',
            ),
            NULL
        );
    }

    

    public function getData($dataType, $univisParam = NULL){
        $sAtts = (!empty($this->atts) && is_array($this->atts) ? implode('-', $this->atts) : '');
        if ($this->noCache){
            $data = $this->univis->getData($dataType, $univisParam);
            set_transient(self::TRANSIENT_PREFIX . $dataType . $sAtts . $this->UnivISOrgNr . $univisParam, $data, self::TRANSIENT_EXPIRATION);
            return $data;
        }
        $data = get_transient(self::TRANSIENT_PREFIX . $dataType . $sAtts . $this->UnivISOrgNr . $univisParam);
        if ($data){
            return $data;
        }else{
            $data = $this->univis->getData($dataType, $univisParam);
            set_transient(self::TRANSIENT_PREFIX . $dataType . $sAtts . $this->UnivISOrgNr . $univisParam, $data, self::TRANSIENT_EXPIRATION);
            return $data;
        }
    }
}