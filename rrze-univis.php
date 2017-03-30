<?php
/**
  Plugin Name: RRZE-UnivIS
  Plugin URI: https://github.com/RRZE-Webteam/rrze-univis
 * Description: Einbindung von Daten aus UnivIS für den Geschäftsverteilungsplan auf Basis des UnivIS-Plugins des Webbaukastens.
 * Version: 1.3.1
 * Author: RRZE-Webteam
 * Author URI: http://blogs.fau.de/webworking/
 * License: GPLv2 or later
 */
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

add_action('plugins_loaded', array('RRZE_UnivIS', 'instance'));

register_activation_hook(__FILE__, array('RRZE_UnivIS', 'activate'));
register_deactivation_hook(__FILE__, array('RRZE_UnivIS', 'deactivate'));
require_once('univis/class_controller.php');


class RRZE_UnivIS {

    const version = '1.3.1';
    const option_name = '_rrze_univis';
    const version_option_name = '_rrze_univis_version';
    const textdomain = 'rrze-univis';
    const php_version = '5.4'; // Minimal erforderliche PHP-Version
    const wp_version = '4.1'; // Minimal erforderliche WordPress-Version

    protected static $instance = null;
    private static $univis_option_page = null;
    private static $univis_url = "http://univis.uni-erlangen.de";

   
    public static function instance() {

        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    private function __construct() {
        // Sprachdateien werden eingebunden.
        load_plugin_textdomain(self::textdomain, false, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));

	self::update_version();   
        
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_options_page'));
        add_shortcode('univis', array($this, 'univis'));

	add_action('admin_init', array($this, 'univis_shortcodes_rte_button'));
        
        add_action('init', array(__CLASS__, 'add_endpoint'));
        add_action('template_redirect', array($this, 'endpoint_template_redirect'));

    }
    
    
    
    private static function get_options() {
        $defaults = self::default_options();

        $options = (array) get_option(self::option_name);
        $options = wp_parse_args($options, $defaults);

        $options = array_intersect_key($options, $defaults);

        return $options;
    }

    private static function default_options() {
        $linktext = '<b><i>Univ</i>IS</b> - Informationssystem der FAU';
        $options = array(
            'univis_default_link' => $linktext,
            'UnivISOrgNr' => '',
        );
        return $options;
    }
    
    private static function get_defaults() {
        $defaults = array(
			'UnivISOrgNr' => '0',
			'task' => 'mitarbeiter-alle',
                        'Personenanzeige_Verzeichnis' => '',
			'Personenanzeige_Bildsuche' =>	'1',
			'Personenanzeige_ZusatzdatenInDatei' =>	'1',
			'Personenanzeige_Publikationen'	=> '1',
			'Personenanzeige_Lehrveranstaltungen' => '1',
                        'Lehrveranstaltung_Verzeichnis' => '',
                        'SeitenCache' => '0',
			'START_SOMMERSEMESTER' => '1.4',
			'START_WINTERSEMESTER' => '1.10',
			'Zeige_Sprungmarken' => '0',
			'OrgUnit' => '',
			'Sortiere_Alphabet' => '0',
			'Sortiere_Jobs' => '1',
                        'Ignoriere_Jobs' => 'Sicherheitsbeauftragter|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte',
                        'Datenverzeichnis' => '',
                        'id' => '',             // kann im Shortcode verwendet werden, sollte aber nicht
                        'lv_id' => '',          // Lehrveranstaltungs-ID
                        'firstname' => '',
                        'lastname' => '',
                        'dozentid' => '',       // ist im Shortcode ein Synonym zu univisid
                        'dozentname' => '',
                        'type' => '',           // für Selektion nach Lehrveranstaltungstypen wie vorl
                        'lv_import' => '1',      // importierte Lehrveranstaltungen werden mit angezeigt, ausblenden über Shortcode
                        'sem' => '',             // Semesterauswahl
                        'univisid' => '',        // ist die Personen-ID, egal ob dozentid oder MA-ID
                        'name' => '',            // Synonym zur Angabe von firstname und lastname
                        'errormsg' => ''          // Anzeige von Fehlermeldungen bei Ausgabe
                );
        return $defaults;
    }



    public static function activate() {
        self::version_compare();
        update_option(self::version_option_name, self::version);
        self::add_endpoint();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }    
    
    private static function version_compare() {
        $error = '';

        if (version_compare(PHP_VERSION, self::php_version, '<')) {
            $error = sprintf(__('Ihre PHP-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die PHP-Version %s.', self::textdomain), PHP_VERSION, self::php_version);
        }

        if (version_compare($GLOBALS['wp_version'], self::wp_version, '<')) {
            $error = sprintf(__('Ihre Wordpress-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die Wordpress-Version %s.', self::textdomain), $GLOBALS['wp_version'], self::wp_version);
        }

        if (!empty($error)) {
            deactivate_plugins(plugin_basename(__FILE__), false, true);
            wp_die($error);
        }
    }

    public static function update_version() {
        if (get_option(self::version_option_name, null) < self::version) {
            // enthalten ab Version 1.3.0, kann später wieder raus
            array(__CLASS__, 'add_endpoint');
            array(__CLASS__, 'flush_rewrite_rules');
            update_option(self::version_option_name, self::version);
        }
    }
    
    public static function add_endpoint() {
        add_rewrite_endpoint('univisid', EP_PAGES);
        add_rewrite_endpoint('lv_id', EP_PAGES);
    }

    public function endpoint_template_redirect() {
        global $wp_query;
        global $univis_data;
        if ( isset($wp_query->query_vars['univisid']) ) {
            $slug = $wp_query->query_vars['univisid'];
            $key = 'univisid';
            $task = 'mitarbeiter-einzeln';
        } elseif ( isset($wp_query->query_vars['lv_id']) ) {
            $slug = $wp_query->query_vars['lv_id'];
            $key = 'lv_id';
            $task = 'lehrveranstaltungen-einzeln';
        } else {
            return;
        }

        if( !empty($slug) ) {
            $atts = array(
                $key => $slug,
            );

            $controller = new univisController($task, NULL, $atts);
            $univis_data = $controller->ladeHTML();
        } else {
            $univis_data = NULL;
        }
        
        if ($template = locate_template('single-univis.php')) {
                $this->load_template($template, $univis_data);
            } else {
                
                $this->load_template(dirname(__FILE__) . '/univis/templates/single-univis.php', $univis_data);
            }   

    }
    
    private function load_template($template, $event = NULL) {
        require_once($template);
        exit();
    }
    
    public static function add_options_page() {
        self::$univis_option_page = add_options_page(__('<b><i>Univ</i>IS</b>', self::textdomain), __('<b><i>Univ</i>IS</b>', self::textdomain), 'manage_options', 'options-univis', array(__CLASS__, 'options_univis'));
        add_action('load-' . self::$univis_option_page, array(__CLASS__, 'univis_help_menu'));
    }

    public static function options_univis() {
        ?>
        <div class="wrap">
        <?php screen_icon(); ?>
            <h2><?php echo __('Einstellungen &rsaquo; <b><i>Univ</i>IS</b>', self::textdomain); ?></h2>

            <form method="post" action="options.php">
        <?php
        settings_fields('univis_options');
        do_settings_sections('univis_options');
        submit_button();
        ?>
            </form>
        </div>
        <?php
    }

    public static function admin_init() {
        register_setting('univis_options', self::option_name, array(__CLASS__, 'options_validate'));
        add_settings_section('univis_default_section', false, '__return_false', 'univis_options');
        add_settings_field('univis_default', __('Linktext zu <b><i>Univ</i>IS</b>', self::textdomain), array(__CLASS__, 'univis_default'), 'univis_options', 'univis_default_section');
        add_settings_field('UnivISOrgNr', __('<b><i>Univ</i>IS</b>-OrgNr.', self::textdomain), array(__CLASS__, 'univis_orgnr'), 'univis_options', 'univis_default_section');        
        add_settings_section('univis_search', false, '__return_false', 'univis_options');
        //add_settings_field('search_lv_id', __('Suche nach Lehrveranstaltungs-ID', self::textdomain), array(__CLASS__, 'search_lv_id'), 'univis_options', 'univis_search');
    }

    public static function options_validate($input) {
        $defaults = self::default_options();
        $options = self::get_options();
        $input['univis_default_link'] = !empty($input['univis_default_link']) ? $input['univis_default_link'] : $defaults['univis_default_link'];
        $input['UnivISOrgNr'] = !empty($input['UnivISOrgNr']) ? $input['UnivISOrgNr'] : $defaults['UnivISOrgNr'];
        return $input;
    }

    public static function univis_default() {
        $options = self::get_options();
        ?>
        <input type='text' name="<?php printf('%s[univis_default_link]', self::option_name); ?>" value="<?php echo $options['univis_default_link']; ?>">
        <?php
    }
    
    public static function univis_orgnr() {
        $options = self::get_options();
        ?>
        <input type='text' name="<?php printf('%s[UnivISOrgNr]', self::option_name); ?>" value="<?php echo $options['UnivISOrgNr']; ?>">
        <?php
    }

    public static function univis_help_menu() {

        $content_univis = array(
            '<p>' . __('<b><i>Univ</i>IS</b>-Daten können im XML-Format über einen Shortcode in die Seiten eingebunden werden.', self::textdomain) . '</p>',
            '<p><strong>' . __('Shortcode:', self::textdomain) . '</strong></p>',
            '<p>' . __('<b>[univis]</b>: bindet den Link zu <b><i>Univ</i>IS</b> ein. Der Linktext kann unten gesetzt werden.', self::textdomain) . '</p>',
            '<p>' . __('<b>[univis number=321601]</b>: liefert alle Informationen zur Org.-Nr. 321601 aus der Institutionendatenbank.', self::textdomain) . '</p>'
        );


        $help_tab_univis = array(
            'id' => 'univis',
            'title' => __('Übersicht', self::textdomain),
            'content' => implode(PHP_EOL, $content_univis),
        );

        $help_sidebar = __('<p><strong>Für mehr Information:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">RRZE-Webteam in Github</a></p>', self::textdomain);

        $screen = get_current_screen();

        if ($screen->id != self::$univis_option_page) {
            return;
        }

        $screen->add_help_tab($help_tab_univis);

        $screen->set_help_sidebar($help_sidebar);
    }

    public static function univis( $atts ) {    
        $univis_url = self::$univis_url;
        $options = self::get_options();
        $defaults = self::get_defaults();
        $univis_link = sprintf('<a href="%1$s">%2$s</a>', $univis_url, $options['univis_default_link']);
        if( empty( $atts )) {
            $ausgabe = $univis_link;
        } else {
            if( isset( $atts['show'] )) { // über show können die Default-Werte (in Großbuchstaben) eingeblendet werden
                $atts['show'] = wp_kses( str_replace(' ', '', $atts['show']), array() );
                $optionen = explode(',', $atts['show']);
                foreach($optionen as $key=>$value) {
                    $atts[$value] = 1;
                }
            }
            if( isset( $atts['hide'] )) { // über hide können die Default-Werte (in Großbuchstaben) ausgeblendet werden
                $atts['hide'] = wp_kses( str_replace(' ', '', $atts['hide']), array() );
                $optionen = explode(',', $atts['hide']);
                foreach($optionen as $key=>$value) {
                    $atts[$value] = 0;
                }
            }
            if( isset( $atts['number'] ) && ctype_digit( $atts['number'] ) ) {
                $atts['UnivISOrgNr'] = wp_kses( $atts['number'], array() );
            } else {
                $atts['UnivISOrgNr'] = $options['UnivISOrgNr'];
            }
            if( isset( $atts['id'] ) && ctype_digit( $atts['id'] ) ) {
                $atts['id'] = wp_kses( $atts['id'], array() );
            }
            if( isset( $atts['dozentid'] ) && ctype_digit( $atts['dozentid'] )) {
                $atts['dozentid'] = wp_kses( $atts['dozentid'], array() );
            }
            if( isset( $atts['univisid'] ) && ctype_digit( $atts['univisid'] )) {
                $atts['univisid'] = wp_kses( $atts['univisid'], array() );
            }
            if( isset( $atts['dozentname'] ) ) {
                $atts['dozentname'] = wp_kses( str_replace(' ', '', $atts['dozentname']), array() );
            }
            if( isset( $atts['name'] ) ) {
                $atts['name'] = wp_kses( str_replace(' ', '', $atts['name']), array() );
            }
            if( isset( $atts['sem'] ) ) {
                $sem = wp_kses( str_replace(' ', '', $atts['sem']), array() );
                if( preg_match( '/[12]\d{3}[ws]/', $sem ) )     $atts['sem'] = $sem;
            }
            if( isset( $atts['id'] ) && isset ( $atts['task'] ) ) {
                switch( $atts['task'] ) {
                    case 'lehrveranstaltungen-einzeln':
                        $atts['lv_id'] = $atts['id'];
                        break;
                    case 'mitarbeiter-einzeln':
                        $atts['univisid'] = $atts['id'];
                        break;
                    case 'lehrveranstaltungen-alle':
                        $atts['univisid'] = $atts['id'];
                        break;
                    default:
                        break;
                }
            }
            if( isset( $atts['task'] ) && $atts['task'] == 'lehrveranstaltungen-alle' ) {
                if( isset($atts['dozentid']) ) {
                    $atts['univisid'] = $atts['dozentid'];
                }
                if( isset($atts['dozentname']) ) {
                    $atts['name'] = $atts['dozentname'];
                }
            }
            if( isset( $atts['ignoriere_jobs'] ) ) { // Übergabe in Großbuchstaben
                $atts['Ignoriere_Jobs'] = wp_kses( str_replace(' ', '', $atts['ignoriere_jobs']), array() );
                $atts['Ignoriere_Jobs'] = wp_kses( str_replace(',', '|', $atts['Ignoriere_Jobs']), array() );
            }
            if( isset( $atts['orgunit'] )) {
                $atts['OrgUnit'] = wp_kses( $atts['orgunit'] );
            }
            
        $shortcode_atts = shortcode_atts( $defaults, $atts );

        extract($shortcode_atts);


        switch( $task ) {
            case 'mitarbeiter-alle':
            case 'mitarbeiter-orga':    
            case 'mitarbeiter-telefonbuch':
            case 'lehrveranstaltungen-alle':
                // Selektion nach Lehrveranstaltungstypen über Shortcodeparameter (z.B. vorl)
                if( $type ) {
                    $controller = new univisController($task, $type, $shortcode_atts);
                    $ausgabe = $controller->ladeHTML();
                    break;                    
                }
            //case 'lehrveranstaltungen-kalender':
            case 'publikationen':
                if( !$UnivISOrgNr ) {
                    $ausgabe = '<p>' . __('Bitte geben Sie eine gültige UnivIS-Organisationsnummer an.', self::textdomain) . '</p>';
                    break;
                }
                $controller = new univisController($task, NULL, $shortcode_atts);
                $ausgabe = $controller->ladeHTML();
                break;
            case 'lehrveranstaltungen-einzeln':
                if( !$lv_id ) {
                    $ausgabe = '<p>' . __('Bitte geben Sie eine gültige Lehrveranstaltungs-ID an.', self::textdomain). '</p>';
                    break;
                } 
                $controller = new univisController($task, NULL, $shortcode_atts);
                $ausgabe = $controller->ladeHTML();
                break;
            case 'mitarbeiter-einzeln':      
                if(  !($name || ($firstname && $lastname) || $univisid) ) {
                    $ausgabe = '<p>' . __('Bitte geben Sie einen Vor- und Nachnamen oder eine UnivIS-ID an.', self::textdomain). '</p>';
                    break;
                } 
                $controller = new univisController($task, NULL, $shortcode_atts);
                $ausgabe = $controller->ladeHTML();
                break;
            default:
                $ausgabe = $univis_link;
            }
        }
        return $ausgabe;
    }
    
    public function univis_shortcodes_rte_button() {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
            add_filter( 'mce_external_plugins', array($this, 'univis_rte_add_buttons' ));
        }
    }

    public function univis_rte_add_buttons( $plugin_array ) {
        $plugin_array['univisrteshortcodes'] = plugin_dir_url(__FILE__) . 'js/tinymce-shortcodes.js';
        return $plugin_array;
    }
    
    public static function search_lv_id() {
        $transient = get_transient(self::search_univis_id_transient);
        $name = isset($transient['name']) ? $transient['name'] : '';
        $lecturer = isset($transient['lecturer']) ? $transient['lecturer'] : '';
        if(class_exists( 'Univis_Data' ) ) {
            $person = sync_helper::get_univisdata(0, $firstname, $givenname);           
        } else {
            $person = array();
        }
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo esc_html(__('Suche nach UnivIS-ID', FAU_PERSON_TEXTDOMAIN)); ?></h2>

            <form method="post">
                <?php
                settings_fields('search_univis_id_options');
                do_settings_sections('search_univis_id_options');
                submit_button(esc_html(__('Person suchen', FAU_PERSON_TEXTDOMAIN)), 'primary', 'settings_page_options-univis');
                ?>
            </form>            
        </div>
        <div class="wrap">
            <?php
                settings_fields('find_univis_id_options');
                do_settings_sections('find_univis_id_options');
                if(empty($person) || empty($person[0])) {
                    echo __('Es konnten keine Daten zur Person gefunden werden. Bitte verändern Sie Ihre Suchwerte und stellen Sie sicher, dass das Plugin Univis-Data aktiviert ist.', FAU_PERSON_TEXTDOMAIN);
                } else {
                    $person = $this->array_orderby($person,"lastname", SORT_ASC, "firstname", SORT_ASC );
                    $no_univis_data = __('keine Daten in UnivIS eingepflegt', FAU_PERSON_TEXTDOMAIN);
                    foreach($person as $key=>$value) {
                        if(array_key_exists('locations', $person[$key]) && array_key_exists('location', $person[$key]['locations'][0]) && array_key_exists('email', $person[$key]['locations'][0]['location'][0])) {
                            $email = $person[$key]['locations'][0]['location'][0]['email'];
                        } else {
                            $email = $no_univis_data;
                        }
                        if(array_key_exists('id', $person[$key])) {
                            $id = $person[$key]['id'];
                        } else {
                            $id = $no_univis_data;
                        }
                        if(array_key_exists('firstname', $person[$key])) {
                            $firstname = $person[$key]['firstname'];
                        } else {
                            $firstname = __('Vorname', FAU_PERSON_TEXTDOMAIN) . ": " . $no_univis_data . ", ";
                        }
                        if(array_key_exists('lastname', $person[$key])) {
                            $lastname = $person[$key]['lastname'];
                        } else {
                            $lastname = __('Nachname', FAU_PERSON_TEXTDOMAIN) . ": " . $no_univis_data;
                        }
                        if(array_key_exists('orgname', $person[$key])) {
                            $orgname = $person[$key]['orgname'];
                        } else {
                            $orgname = $no_univis_data;
                        }
                        //echo sprintf(__('UnivIS-ID %1$s: %2$s %3$s, E-Mail: %4$s, Organisation: %5$s', FAU_PERSON_TEXTDOMAIN), $id, $firstname, $lastname, $email, $orgname);
                        //$output = __('UnivIS-ID', FAU_PERSON_TEXTDOMAIN) . ' '. $id . ': '. $firstname . ' ' . $lastname . ', ' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': ' . $email. ', ' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': ' . $orgname;
                        echo 'UnivIS-ID '. $id . ': '. $firstname . ' ' . $lastname . ', E-Mail: ' . $email. ', Organisation: ' . $orgname;
                        echo "<br>";
                    }
                }
            ?>
        </div>
        <?php
            delete_transient(self::search_univis_id_transient);
    }
    
    
    
    ///////////////////////////////////////////////////////////////
    /////		Hilfsmethoden
    ///////////////////////////////////////////////////////////////
    // XML Parser
    private static function xml2array($fname) {
        //$sxi = $fname;
        $sxi = new SimpleXmlIterator($fname, null, true);
        return self::sxiToArray($sxi);
    }

    private static function sxiToArray($sxi) {
        $a = array();

        for ($sxi->rewind(); $sxi->valid(); $sxi->next()) {
            if (!array_key_exists($sxi->key(), $a)) {
                $a[$sxi->key()] = array();
            }
            if ($sxi->hasChildren()) {
                $a[$sxi->key()][] = self::sxiToArray($sxi->current());
            } else {
                $a[$sxi->key()] = strval($sxi->current());

                //Fuege die UnivisRef Informationen ein.
                if ($sxi->UnivISRef) {
                    $attributes = (array) $sxi->UnivISRef->attributes();
                    $a[$sxi->key()][] = $attributes["@attributes"];
                }
            }

            if ($sxi->attributes()) {
                $attributes = (array) $sxi->attributes();
                $a["@attributes"] = $attributes["@attributes"];
            }
        }
        return $a;
    }
    

}
