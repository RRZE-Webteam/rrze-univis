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
        // add_action( 'init',  [$this, 'initGutenberg'] );
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
        // merge given attributes with default ones
        $atts_default = array();
        foreach( $this->settings as $k => $v ){
            if ( $k != 'block' ){
                $atts_default[$k] = $v['default'];
            }
        }
        $atts = shortcode_atts( $atts_default, $atts );
        $data = '';

        $univis = new UnivISAPI('https://univis.uni-erlangen.de', $this->UnivISOrgNr);

        switch($atts['task']){
            case 'mitarbeiter-einzeln': 
                if (isset($atts['univisid'])){
                    $data = $univis->getData('personByID', $atts['univisid']);
                    if ($data){
                        $atts['name'] = $data[0]['lastname'] . ',' . $data[0]['firstname'];
                    }
                }elseif(isset($atts['name'])){
                    $data = $univis->getData('personByName', $atts['name']);
                }
                if ($data && !empty($atts['name'])){
                    $person = $data[0];
                    $person['lectures'] = $univis->getData('lectureByName', $atts['name']);
                }
                break;
            case 'mitarbeiter-orga': 
                $show_location = 0;
                $data = $univis->getData('personByOrga');
                break;
            case 'mitarbeiter-telefonbuch': 
                $show_location = 1;
                $show_jumpmark = 1;
                $data = $univis->getData('personByOrgaPhonebook');
                break;
            case 'mitarbeiter-alle': 
                $data = $univis->getData('personAll');
                $show_location = 0;
                break;
            case 'lehrveranstaltungen-einzeln': 
                if (isset($atts['lv_id'])){
                    $data = $univis->getData('lectureByID', $atts['lv_id']);
                    if ($data){
                        $veranstaltung = $data[array_key_first($data)][0];
                    }
                }
                break;
            case 'lehrveranstaltungen-alle': 
                $data = $univis->getData('lectureByDepartment');
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
