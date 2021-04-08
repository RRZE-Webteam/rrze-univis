<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;
use function RRZE\UnivIS\Config\getShortcodeSettings;


/**
 * Shortcode
 */
class Shortcode
{

    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;
    protected $UnivISOrgNr;
    protected $UnivISLink;

    /**
     * Settings-Objekt
     * @var object
     */
    private $settings = '';

    /**
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    public function __construct($pluginFile, $settings)
    {
        $this->pluginFile = $pluginFile;
        $this->settings = getShortcodeSettings();
        add_action( 'admin_enqueue_scripts', [$this, 'enqueueGutenberg'] );
        $options = get_option( 'rrze-univis' );
        $this->UnivISOrgNr = (!empty($options['basic_UnivISOrgNr']) ? $options['basic_UnivISOrgNr'] : 0);
        $this->UnivISLink = sprintf('<a href="%1$s">%2$s</a>', (!empty($options['basic_univis_url']) ? $options['basic_univis_url'] : __('URL zu UnivIS fehlt', 'rrze-univis')), (!empty($options['basic_univis_linktxt']) ? $options['basic_univis_linktxt'] : __('Text zum UnivIS Link fehlt', 'rrze-univis')));
    }

    /**
     * Er wird ausgeführt, sobald die Klasse instanziiert wird.
     * @return void
     */
    public function onLoaded()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('univis', [$this, 'shortcodeOutput'], 10, 2);
    }

    /**
     * Enqueue der Skripte.
     */
    public function enqueueScripts()
    {
        wp_register_style('rrze-univis-shortcode', plugins_url('assets/css/shortcode.css', plugin_basename($this->pluginFile)));
        wp_register_script('rrze-univis-shortcode', plugins_url('assets/js/shortcode.js', plugin_basename($this->pluginFile)));
    }


    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput( $atts ) {

        if (empty($atts)){
            return $this->UnivISLink;
        }

        // lv_id is not in config (=> id)
        if (!empty($atts['lv_id'])){
            $atts['id'] = (int)$atts['lv_id'];
            if ($atts['task'] == 'lehrveranstaltungen-alle'){
                $atts['task'] = 'lehrveranstaltungen-einzeln';
            }
        }

        // merge given attributes with default ones
        $atts_default = array();
        foreach( $this->settings as $k => $v ){
            if ( $k != 'block' ){
                $atts_default[$k] = $v['default'];
            }
        }
        $atts = shortcode_atts( $atts_default, $atts );

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

        $hide_jobs = NULL;
        if (!empty($atts['ignoriere_jobs'])){
            $hide_jobs = explode('|', $atts['ignoriere_jobs']);
        }
        $show_jobs = NULL;
        if (!empty($atts['zeige_jobs'])){
            $show_jobs = explode('|', $atts['zeige_jobs']);
        }

        $show = [];
        if (!empty($atts['show'])){
            $show = explode(',', trim(strtolower($atts['show'])));
            if (($key = array_search('zeige_sprungmarken', $show)) !== false) {
                unset($show[$key]);
                $show[] = 'sprungmarken';
            }
        }

        $hide = [];
        if (!empty($atts['hide'])){
            $hide = explode(',', trim(strtolower($atts['hide'])));
            if (in_array(['sprungmarken', 'zeige_sprungmarken'], $hide)){
                if (($key = array_search('sprungmarken', $show)) !== false) {
                    unset($show[$key]);
                }
            }
        }

        $univis = new UnivISAPI('https://univis.uni-erlangen.de', $this->UnivISOrgNr);


        // atts vom alten Plugin:
        //
        // DONE show => sprungmarken, telefon, mail
        // DONE hide => sprungmarken
        // ignoriere_jobs z.B. ignoriere_jobs="Webmaster, Postmaster"
        // zeige_jobs z.B. zeige_jobs="Webmaster, Postmaster"
        // sem
        // sprache
        // orgunit ?
        // lv-typ = type => [... type="vorl"] => nur Vorlesungen
        //
        //
        //
        // DONE [univis] => gibt nur Link zu UnivIS aus
        // DONE [univis number="420100"] => 'mitarbeiter-orga' überschreibt default univisID
        // DONE [univis task="mitarbeiter-alle"]
        // DONE [univis task="mitarbeiter-alle" number="420100"]
        // DONE [univis task="mitarbeiter-orga"]
        // DONE [univis task="mitarbeiter-orga" number="420100"]
        // DONE [univis task="mitarbeiter-telefonbuch" show="zeige_sprungmarken"] lowercase
        //
        // [univis task="mitarbeiter-telefonbuch" ignoriere_jobs="Webmaster, Postmaster"]
        // Automatisch werden Personen mit folgenden Zuordnungen ausgeblendet: 
        // Sicherheitsbeauftragter, 
        // IT-Sicherheits-Beauftragter, 
        // Webmaster, 
        // Postmaster, 
        // IT-Betreuer
        // UnivIS-Beauftragte
        //
        // DONE [univis task="mitarbeiter-einzeln" name="Mustermann,Max"]
        // DONE [univis task="mitarbeiter-einzeln" univisid="40858741"]
        //
        // DONE [univis task="lehrveranstaltungen-alle"]
        // DONE [univis task="lehrveranstaltungen-alle" number="420100"]
        // Bei der Anzeige von Lehrveranstaltungen wird automatisch das Semester angezeigt, das gerade bei UnivIS als aktuelles Semester eingestellt ist.
        //
        // [univis task="lehrveranstaltungen-alle" type="vorl"] => nur Vorlesungen
        // DONE [univis task="lehrveranstaltungen-alle" name="Mustermann,Max"]
        // [univis task="lehrveranstaltungen-alle" univisid="20333881"] univisid ist die vom Professor
        // [univis task="lehrveranstaltungen-alle" sem="2016w"]
        // [univis task="lehrveranstaltungen-alle" lv_import="0"] => importierte Lehrveranstaltungen ausblenden
        // [univis task="lehrveranstaltungen-alle" sprache="E"]
        //
        // DONE [univis task="lehrveranstaltungen-einzeln" lv_id="41105306"]
        // DONE [univis task="lehrveranstaltungen-alle" lv_id="41105306"]
        // [univis task="lehrveranstaltungen-alle" lv_id="41105306" sem="2016w"]
        //
        // DONE [univis task="publikationen"]
        // DONE [univis task="publikationen" number="420100"] 
        //



        $data = '';

        switch($atts['task']){
            case 'mitarbeiter-einzeln': 
                if (!empty($atts['id'])){
                    $data = $univis->getData('personByID', $atts['id']);
                    if ($data){
                        $atts['name'] = $data[0]['lastname'] . ',' . $data[0]['firstname'];
                    }
                }elseif(!empty($atts['name'])){
                    $data = $univis->getData('personByName', $atts['name']);
                }
                if ($data && !empty($atts['name'])){
                    $person = $data[0];
                    $person['lectures'] = $univis->getData('lectureByName', $atts['name']);
                }
                break;
            case 'mitarbeiter-orga': 
                $data = $univis->getData('personByOrga');
                break;
            case 'mitarbeiter-telefonbuch': 
                $data = $univis->getData('personByOrgaPhonebook', NULL, 1, $show_jobs, $hide_jobs);
                break;
            case 'mitarbeiter-alle': 
                $data = $univis->getData('personAll');
                break;
            case 'lehrveranstaltungen-einzeln': 
                if (!empty($atts['id'])){
                    $data = $univis->getData('lectureByID', $atts['id']);
                }elseif (!empty($atts['name'])){
                    $data = $univis->getData('lectureByName', $atts['name']);
                }elseif (!empty($atts['univisid'])){
                    $data = $univis->getData('lectureByNameID', $atts['univisid']);
                }
                if ($data){
                    $veranstaltung = $data[array_key_first($data)][0];
                }
                break;
            case 'lehrveranstaltungen-alle': 
                if (!empty($atts['name'])){
                    $data = $univis->getData('lectureByName', $atts['name']);
                }elseif (!empty($atts['univisid'])){
                    $data = $univis->getData('lectureByNameID', $atts['univisid']);
                }else{
                    $data = $univis->getData('lectureByDepartment');
                }
                break;
            case 'publikationen': 
                $data = $univis->getData('publicationByDepartment', NULL, 1);
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
            echo 'no data found';
        }

        // wp_enqueue_style('rrze-univis-shortcode');
        // wp_enqueue_script('rrze-univis-shortcode');
    }

    public function isGutenberg(){
        $postID = get_the_ID();
        if ($postID && !use_block_editor_for_post($postID)){
            return false;
        }

        return true;        
    }

    public function fillGutenbergOptions() {
        // Example:
        // fill select id ( = glossary )
        $glossaries = get_posts( array(
            'posts_per_page'  => -1,
            'post_type' => 'glossary',
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        $this->settings['id']['field_type'] = 'multi_select';
        $this->settings['id']['default'] = array(0);
        $this->settings['id']['type'] = 'array';
        $this->settings['id']['items'] = array( 'type' => 'number' );
        $this->settings['id']['values'][] = ['id' => 0, 'val' => __( '-- all --', 'rrze-basis' )];
        foreach ( $glossaries as $glossary){
            $this->settings['id']['values'][] = [
                'id' => $glossary->ID,
                'val' => str_replace( "'", "", str_replace( '"', "", $glossary->post_title ) )
            ];
        }

        return $this->settings;
    }


    public function initGutenberg() {
        if (! $this->isGutenberg()){
            return;
        }

        // get prefills for dropdowns
        // $this->settings = $this->fillGutenbergOptions();

        // register js-script to inject php config to call gutenberg lib
        $editor_script = $this->settings['block']['blockname'] . '-block';        
        $js = '../assets/js/' . $editor_script . '.js';

        wp_register_script(
            $editor_script,
            plugins_url( $js, __FILE__ ),
            array(
                'RRZE-Gutenberg',
            ),
            NULL
        );
        wp_localize_script( $editor_script, $this->settings['block']['blockname'] . 'Config', $this->settings );

        // register block
        register_block_type( $this->settings['block']['blocktype'], array(
            'editor_script' => $editor_script,
            'render_callback' => [$this, 'shortcodeOutput'],
            'attributes' => $this->settings
            ) 
        );
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
