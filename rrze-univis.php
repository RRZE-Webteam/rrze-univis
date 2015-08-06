<?php
/**
 * Plugin Name: RRZE-UnivIS
 * Description: Einbindung von Daten aus UnivIS für den Geschäftsverteilungsplan auf Basis des UnivIS-Plugins des Webbaukastens.
 * Version: 1.0.4
 * Author: RRZE-Webteam (Karin Kimpan)
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

    const version = '1.0.4';
    const option_name = '_rrze_univis';
    const version_option_name = '_rrze_univis_version';
    const textdomain = 'rrze-univis';
    const php_version = '5.3'; // Minimal erforderliche PHP-Version
    const wp_version = '3.8'; // Minimal erforderliche WordPress-Version

    protected static $instance = null;
    private static $univis_option_page = null;
    private static $univis_url = "http://univis.uni-erlangen.de";

    public static function instance() {

        if (null == self::$instance) {
            self::$instance = new self;
            self::$instance->init();
        }

        return self::$instance;
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
            
        );
        return $options;
    }
    
    private static function get_defaults() {
        $defaults = array(
			'UnivISOrgNr' => '100206',
			'task' => '',
			'Personenanzeige_Verzeichnis' => '',
			'firstname'=>'',
			'lastname'=>'',
			'wpuserid'=>'',
			'univis_id'=>'',
			'Personenanzeige_Bildsuche' =>	'0',
			'Personenanzeige_ZusatzdatenInDatei' =>	'0',
			'Personenanzeige_Publikationen'	=> '0',
			'Personenanzeige_Lehrveranstaltungen' => '0',
			'Lehrveranstaltung_Verzeichnis' => '',
			'SeitenCache' => '0',//Datenverfallszeit aus cache in sekunden
			'START_SOMMERSEMESTER' => '1.7.',
			'START_WINTERSEMESTER' => '31.12.',//Start WS muss nach Start SS liegen!!!
			'semester' => '',
			'Zeige_Sprungmarken' => '1',
			'OrgUnit' => '',
			'Sortiere_Alphabet' => '0',
			'Sortiere_Jobs' => '1',
            'Ignoriere_Jobs' => 'Verwaltung',
'Ehemalige_Groupname' => '',//Personen in dieser Gruppe die noch andere Aufgaben haben werden nicht als ehemalig geführt
'Leader_Jobs' => 'Leitung|Gruppenleiter|Vorzimmer',
'Function_Jobs' => 'Gastwissenschaftler/-in|Sicherheitsbeauftragter (nach SGB VII)|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte|Ehemalige/r Mitarbeiter/-in',
      'Datenverzeichnis' => ''
	);
        return $defaults;
    }

    private function init() {
        load_plugin_textdomain(self::textdomain, false, dirname(plugin_basename(__FILE__)) . '/languages/');
        add_action('admin_init', array(__CLASS__, 'admin_init'));
        add_action('admin_menu', array(__CLASS__, 'add_options_page'));
        add_shortcode('univis', array(__CLASS__, 'univis'));
    }

    public static function activate() {
        self::version_compare();
        update_option(self::version_option_name, self::version);
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
        if (get_option(self::version_option_name, null) != self::version)
            update_option(self::version_option_name, self::version);
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
    }

    public static function options_validate($input) {
        $defaults = self::default_options();
        $options = self::get_options();
        $input['univis_default_link'] = !empty($input['univis_default_link']) ? $input['univis_default_link'] : $defaults['univis_default_link'];
        return $input;
    }

    public static function univis_default() {
        $options = self::get_options();
        ?>
        <input type='text' name="<?php printf('%s[univis_default_link]', self::option_name); ?>" value="<?php echo $options['univis_default_link']; ?>">
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

   public static function univis($atts, $content = null ) {
        $univis_url = self::$univis_url;
        $options = self::get_options();
        $defaults = self::get_defaults();
       
        $shortcode_atts = shortcode_atts( $defaults, $atts);
        //extract($shortcode_atts);

        if ($shortcode_atts['UnivISOrgNr']) {
            // FETCH $_GET OR CRON ARGUMENTS TO AUTOMATE TASKS
            if(isset($argv[1])) {
                $args = (!empty($_GET)) ? $_GET:array('task'=>$argv[1]);
            }
  
            		$controller = new univisController($shortcode_atts);
            		$ausgabe = $controller->ladeHTML();
          

        } else
            $ausgabe = sprintf('<a href="%1$s">%2$s</a>', $univis_url, $options['univis_default_link']);

        return $ausgabe;
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
