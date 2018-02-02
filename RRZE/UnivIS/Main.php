<?php

namespace RRZE\UnivIS;

use RRZE\UnivIS\Core\Options;
use RRZE\UnivIS\Core\Settings;

defined('ABSPATH') || exit;

class Main {

    public $plugin_basename;
    public $options;
    public $settings;
    public $controller;

    /**
     * UnivIS Url
     *
     * @var string
     * @access private
     */
    private $univis_url = 'http://univis.uni-erlangen.de';

    public $language = [
        'suffix' => '', 
        'orgunit' => 'orgunit', 
        'orgunits' => 'orgunits', 
        'orgname' => 'orgname', 
        'description' => 'description', 
        'text' => 'text',
        'title' => 'title'
    ];
    
    public function __construct($plugin_basename = NULL) {
        $this->plugin_basename = $plugin_basename;

        $this->options = new Options();

        $this->settings = new Settings();

        add_action('admin_menu', array($this->settings, 'admin_settings_page'));
        add_action('admin_init', array($this->settings, 'admin_settings'));

        add_shortcode('univis', array($this, 'add_shortcode'));

        add_action('init', 'RRZE\UnivIS\add_endpoint');
        add_action('template_redirect', array($this, 'endpoint_template_redirect'));
    }

    public function add_shortcode($atts) {
        $options = $this->options->get_options();
        $defaults = $this->default_atts();

        $univis_link = sprintf('<a href="%1$s">%2$s</a>', $this->univis_url, $options->univis_default_link);
        if (empty($atts)) {
            $ausgabe = $univis_link;
        } else {
            if (isset($atts['show'])) { // über show können die Default-Werte (in Großbuchstaben) eingeblendet werden
                $atts['show'] = wp_kses(str_replace(' ', '', $atts['show']), array());
                $optionen = explode(',', $atts['show']);
                foreach ($optionen as $key => $value) {
                    $atts[$value] = 1;
                }
            }

            if (isset($atts['hide'])) { // über hide können die Default-Werte (in Großbuchstaben) ausgeblendet werden
                $atts['hide'] = wp_kses(str_replace(' ', '', $atts['hide']), array());
                $optionen = explode(',', $atts['hide']);
                foreach ($optionen as $key => $value) {
                    $atts[$value] = 0;
                }
            }

            if (isset($atts['number']) && ctype_digit($atts['number'])) {
                $atts['UnivISOrgNr'] = wp_kses($atts['number'], array());
            } else {
                $atts['UnivISOrgNr'] = $options->UnivISOrgNr;
            }

            if (isset($atts['id']) && ctype_digit($atts['id'])) {
                $atts['id'] = wp_kses($atts['id'], array());
            }

            if (isset($atts['dozentid']) && ctype_digit($atts['dozentid'])) {
                $atts['dozentid'] = wp_kses($atts['dozentid'], array());
            }

            if (isset($atts['univisid']) && ctype_digit($atts['univisid'])) {
                $atts['univisid'] = wp_kses($atts['univisid'], array());
            }

            if (isset($atts['dozentname'])) {
                $atts['dozentname'] = wp_kses(str_replace(' ', '', $atts['dozentname']), array());
            }

            if (isset($atts['name'])) {
                $atts['name'] = wp_kses(str_replace(' ', '', $atts['name']), array());
            }

            if (isset($atts['sem'])) {
                $sem = wp_kses(str_replace(' ', '', $atts['sem']), array());
                if (preg_match('/[12]\d{3}[ws]/', $sem))
                    $atts['sem'] = $sem;
            }

            if (isset($atts['sprache'])) {
                $sprache = wp_kses(str_replace(' ', '', $atts['sprache']), array());
                if (strpbrk($sprache, 'DE') != FALSE && str_word_count($sprache) == 1) {
                    $atts['leclanguage'] = $sprache;
                }
            }

            if (isset($atts['id']) && isset($atts['task'])) {
                switch ($atts['task']) {
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

            if (isset($atts['task']) && $atts['task'] == 'lehrveranstaltungen-alle') {
                if (isset($atts['dozentid'])) {
                    $atts['univisid'] = $atts['dozentid'];
                }
                if (isset($atts['dozentname'])) {
                    $atts['name'] = $atts['dozentname'];
                }
            }

            if (isset($atts['ignoriere_jobs'])) { // Übergabe in Großbuchstaben
                $atts['Ignoriere_Jobs'] = wp_kses(str_replace(', ', ',', $atts['ignoriere_jobs']), array());
                $atts['Ignoriere_Jobs'] = wp_kses(str_replace(',', '|', $atts['Ignoriere_Jobs']), array());
            }

            if (isset($atts['zeige_jobs'])) { // Übergabe in Großbuchstaben
                $zeige_jobs = wp_kses(str_replace(', ', ',', $atts['zeige_jobs']), array());
                $atts['Zeige_Jobs'] = explode(',', $zeige_jobs);
            }

            if (isset($atts['orgunit'])) {
                $atts['OrgUnit'] = wp_kses($atts['orgunit'], array());
            }

            if (isset($atts['lv-typ'])) {
                $atts['lv_type'] = wp_kses($atts['lv-typ'], array());
            }

            if (isset($atts['lang'])) {
                if ($atts['lang'] == 'en') {
                    $atts['lang'] = $this->set_language('_en');
                } elseif ($atts['lang'] == 'de') {
                    $atts['lang'] = $this->set_language('');
                    // NUR FÜR _rrze_debug
//                } elseif ( $atts['lang'] == 'test' ) {
//                    $atts['lang'] = $this->set_language('_test');
                } else {
                    $atts['lang'] = $defaults['lang'];
                }
            }


            $shortcode_atts = shortcode_atts($defaults, $atts);

            extract($shortcode_atts);

            switch ($task) {
                case 'mitarbeiter-alle':
                case 'mitarbeiter-orga':
                case 'mitarbeiter-telefonbuch':
                case 'lehrveranstaltungen-alle':
                    // Selektion nach Lehrveranstaltungstypen über Shortcodeparameter (z.B. vorl)
                    if ($type) {
                        $controller = new Controller($task, $type, $shortcode_atts);
                        $ausgabe = $controller->ladeHTML();
                        break;
                    }
                case 'publikationen':
                    if (!$UnivISOrgNr) {
                        $ausgabe = '<p>' . __('Please enter a valid UnivIS OrgNr.', 'rrze-univis') . '</p>';
                        break;
                    }
                    $controller = new Controller($task, NULL, $shortcode_atts);
                    $ausgabe = $controller->ladeHTML();
                    break;
                case 'lehrveranstaltungen-einzeln':
                    if (!$lv_id) {
                        $ausgabe = '<p>' . __('Please enter a valid lecture ID.', 'rrze-univis') . '</p>';
                        break;
                    }
                    $controller = new Controller($task, NULL, $shortcode_atts);
                    $ausgabe = $controller->ladeHTML();
                    break;
                case 'mitarbeiter-einzeln':
                    if (!($name || ($firstname && $lastname) || $univisid)) {
                        $ausgabe = '<p>' . __('Please enter a first and last name or a UnivIS ID.', 'rrze-univis') . '</p>';
                        break;
                    }
                    $controller = new Controller($task, NULL, $shortcode_atts);
                    $ausgabe = $controller->ladeHTML();
                    break;
                default:
                    $ausgabe = $univis_link;
            }
        }
        return $ausgabe;
    }

    /*
     * Standard Shortcode-Attribute
     * @return array
     */

    public function default_atts() {
        $lang = get_locale();
        if (strpos($lang, 'en_') === 0) {
            $language = $this->set_language('_en');
        } else {
            $language = $this->set_language('');
        }

        $atts = [
            'UnivISOrgNr' => '0',
            'task' => 'mitarbeiter-alle',
            'Personenanzeige_Verzeichnis' => '',
            'Personenanzeige_Bildsuche' => '1',
            'Personenanzeige_ZusatzdatenInDatei' => '1',
            'Personenanzeige_Publikationen' => '1',
            'Personenanzeige_Lehrveranstaltungen' => '1',
            'Lehrveranstaltung_Verzeichnis' => '',
            'SeitenCache' => '0',
            'START_SOMMERSEMESTER' => '1.4',
            'START_WINTERSEMESTER' => '1.10',
            'Zeige_Sprungmarken' => '0',
            'OrgUnit' => '',
            'Sortiere_Alphabet' => '0',
            'Sortiere_Jobs' => '1',
            'Ignoriere_Jobs' => [
                '_de' => 'Sicherheitsbeauftragter|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte',
                '_en' => 'Security commissary|IT-security commissary|Webmaster|Postmaster|IT-support|Local UnivIS administration',
            ],
            'Zeige_Jobs' => [],
            'Datenverzeichnis' => '',
            'id' => '', // kann im Shortcode verwendet werden, sollte aber nicht
            'lv_id' => '', // Lehrveranstaltungs-ID
            'firstname' => '',
            'lastname' => '',
            'dozentid' => '', // ist im Shortcode ein Synonym zu univisid
            'dozentname' => '',
            'type' => '', // für Selektion nach Lehrveranstaltungstypen wie vorl
            'lv_import' => '1', // importierte Lehrveranstaltungen werden mit angezeigt, ausblenden über Shortcode
            'sem' => '', // Semesterauswahl
            'univisid' => '', // ist die Personen-ID, egal ob dozentid oder MA-ID
            'name' => '', // Synonym zur Angabe von firstname und lastname
            'errormsg' => '', // Anzeige von Fehlermeldungen bei Ausgabe
            'lv_type' => '1', // Anzeige LV-Typ-Überschriften 
            'lang' => $language, // wichtig für die Ausgabe englischer Bezeichnungen von orgunit, orgunits, text, description
            'leclanguage' => '', // Veranstaltungssprache
            'kompakt' => 0              // Ausschließliche Anzeige LV-Überschriften
        ];

        return $atts;
    }

    private function set_language($lang) {
        $language = $this->language;
        foreach ($language as $key => &$value) {
            if ($key == 'orgunits') {
                $value = 'orgunit' . $lang . 's';
            } else {
                $value = $value . $lang;
            }
        }
        return $language;
    }

    public function endpoint_template_redirect() {
        global $wp_query;

        if (isset($wp_query->query_vars['univisid'])) {
            $slug = $wp_query->query_vars['univisid'];
            $key = 'univisid';
            $task = 'mitarbeiter-einzeln';
        } elseif (isset($wp_query->query_vars['lv_id'])) {
            $slug = $wp_query->query_vars['lv_id'];
            $key = 'lv_id';
            $task = 'lehrveranstaltungen-einzeln';
        } else {
            return;
        }

        if (!empty($slug)) {
            $slug = $key . '=' . $slug;
            $slugs = explode('&', $slug);
            $atts = array();

            foreach ($slugs as $k => $v) {
                $arr = explode('=', $v);
                $atts[$arr[0]] = $arr[1];
            }

            $controller = new Controller($task, NULL, $atts);

            $univis_data = $controller->ladeHTML();
        } else {
            $univis_data = NULL;
        }

        if ($template = locate_template('single-univis.php')) {
            $this->load_template($template, $univis_data);
        } else {
            $this->load_template('Templates/single-univis.php', $univis_data);
        }
    }

    protected function load_template($template, $daten = array()) {
        //$data['messages'] = $this->messages;

        return include $template;
    }

}
