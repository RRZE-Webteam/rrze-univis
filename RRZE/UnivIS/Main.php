<?php

namespace RRZE\UnivIS;

use RRZE\UnivIS\Controller;
use RRZE\UnivIS\Core\Options;
use RRZE\UnivIS\Core\Settings;

defined('ABSPATH') || exit;

class Main
{
    /**
     * @var string
     * @access public
     */      
    public $plugin_file;

    /**
     * @var object
     * @access public
     */
    public $options;
    
    /**
     * @var object
     * @access public
     */    
    public $settings;
    
    /**
     * @var object
     * @access public
     */    
    public $controller;

    /**
     * UnivIS Url
     *
     * @var string
     * @access public
     */
    public $univis_url = 'https://univis.uni-erlangen.de';

    /**
     * @var array
     * @access public
     */
    public $language = [
        'suffix' => '',
        'orgunit' => 'orgunit',
        'orgunits' => 'orgunits',
        'orgname' => 'orgname',
        'description' => 'description',
        'text' => 'text',
        'title' => 'title'
    ];
    
    /**
     * @var array
     * @access public
     */    
    public $allowed_stylesheets = [
        'FAU' => [
            'FAU-Einrichtungen',
            'FAU-Einrichtungen-BETA',
            'FAU-Medfak',
            'FAU-RWFak',
            'FAU-Philfak',
            'FAU-Techfak',
            'FAU-Natfak'
        ],
        'RRZE' => [
            'rrze-2015'
        ],
        'Blue-Edgy' => [
            'blue-edgy'
        ],
        'FAU-Events' => [
            'FAU-Events'
        ]
    ];
    
    public function __construct($plugin_file = null)
    {
        $this->plugin_file = $plugin_file;

        $this->options = new Options();
        $this->settings = new Settings();        
        $this->controller = new Controller();

        add_action('admin_menu', array($this->settings, 'admin_settings_page'));
        add_action('admin_init', array($this->settings, 'admin_settings'));

        add_shortcode('univis', array($this, 'add_shortcode'));
        add_action('admin_init', array($this, 'mce_external_plugins'));

        add_action('init', 'RRZE\UnivIS\add_endpoint');
        add_action('template_redirect', array($this, 'endpoint_template_redirect'));
    }

    public function add_shortcode($atts)
    {
        $options = $this->options->get_options();
        $defaults = $this->default_atts();

        $univis_link = sprintf('<a href="%1$s">%2$s</a>', $this->univis_url, $options->univis_default_link);
        if (empty($atts)) {
            $ausgabe = $univis_link;
        } else {
            $atts = array_change_key_case($atts);

            if (isset($atts['task']) && ($atts['task']=='mitarbeiter-alle')) {
                $defaults['telefon'] = 1;
            }
            _rrze_debug($atts);
            //_rrze_debug($defaults);
            if (isset($atts['show'])) {
                $show = trim(preg_replace('/\s+/', '', $atts['show']));
                $show = explode(',', $show);
                foreach ($show as $value) {
                    if (strtolower($value) == 'sprungmarken') $value = 'zeige_sprungmarken';
                    if (strtolower($value) == 'jobs') $value = 'zeige_jobs';
                    $atts[strtolower($value)] = 1;
                }
            }

            if (isset($atts['hide'])) {
                $hide = trim(preg_replace('/\s+/', '', $atts['hide']));
                $hide = explode(',', $hide);
                foreach ($hide as $value) {
                    if (strtolower($value) == 'sprungmarken') $value = 'zeige_sprungmarken';
                    if (strtolower($value) == 'jobs') $value = 'ignoriere_jobs';
                    $atts[strtolower($value)] = '';
                }
            }
                        _rrze_debug($atts);
            $atts['UnivISOrgNr'] = isset($atts['number']) && absint($atts['number']) ? absint($atts['number']) : $options->UnivISOrgNr;
            
            if (isset($atts['id']) && absint($atts['id'])) {
                $atts['id'] = absint($atts['id']);
            }

            if (isset($atts['dozentid']) && absint($atts['dozentid'])) {
                $atts['dozentid'] = absint($atts['dozentid']);
            }

            if (isset($atts['univisid']) && absint($atts['univisid'])) {
                $atts['univisid'] = absint($atts['univisid']);
            }
            
            if (isset($atts['dozentname'])) {
                $atts['dozentname'] = wp_kses(trim(preg_replace('/\s+/', ' ', $atts['dozentname'])), []);
            }

            if (isset($atts['name'])) {
                $atts['name'] = wp_kses(trim(preg_replace('/\s+/', ' ', $atts['name'])), []);
            }

            if (isset($atts['sem'])) {
                $sem = wp_kses(trim(preg_replace('/\s+/', ' ', $atts['sem'])), []);
                if (preg_match('/[12]\d{3}[ws]/', $sem)) {
                    $atts['sem'] = $sem;
                }
            }

            if (isset($atts['sprache'])) {
                $sprache = wp_kses(str_replace(' ', '', $atts['sprache']), array());
                if (strpbrk($sprache, 'DE') != false && str_word_count($sprache) == 1) {
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

            if (isset($atts['ignoriere_jobs'])) {
                $atts['ignoriere_jobs'] = wp_kses(str_replace(', ', ',', $atts['ignoriere_jobs']), array());
                $atts['ignoriere_jobs'] = wp_kses(str_replace(',', '|', $atts['ignoriere_jobs']), array());
            }

            if (isset($atts['zeige_jobs'])) {
                $zeige_jobs = wp_kses(str_replace(', ', ',', $atts['zeige_jobs']), array());
                $atts['zeige_jobs'] = explode(',', $zeige_jobs);
            }

            if (isset($atts['orgunit'])) {
                $atts['orgunit'] = wp_kses($atts['orgunit'], array());
            }

            if (isset($atts['lv-typ'])) {
                $atts['lv_type'] = wp_kses($atts['lv-typ'], array());
            }

            if (isset($atts['lang'])) {
                if ($atts['lang'] == 'en') {
                    $atts['lang'] = $this->set_language('_en');
                } elseif ($atts['lang'] == 'de') {
                    $atts['lang'] = $this->set_language('');
                } else {
                    $atts['lang'] = $defaults['lang'];
                }
            }

            $shortcode_atts = shortcode_atts($defaults, $atts);
            do_action('rrze.log.debug', ['plugin' => 'rrze-univis', 'shortcode atts' => $shortcode_atts]);
            extract($shortcode_atts);
            
            switch ($task) {
                case 'mitarbeiter-alle':
                case 'mitarbeiter-orga':
                case 'mitarbeiter-telefonbuch':
                case 'lehrveranstaltungen-alle':
                    // Selektion nach Lehrveranstaltungstypen über Shortcodeparameter (z.B. vorl)
                    if ($type) {
                        $this->controller->init($task, $shortcode_atts);
                        $ausgabe = $this->controller->ladeHTML();
                        break;
                    }
                    // no break
                case 'publikationen':
                    if (!$UnivISOrgNr) {
                        $ausgabe = '<p>' . __('Please enter a valid UnivIS OrgNr.', 'rrze-univis') . '</p>';
                        break;
                    }
                    $this->controller->init($task, $shortcode_atts);
                    $ausgabe = $this->controller->ladeHTML();
                    break;
                case 'lehrveranstaltungen-einzeln':
                    if (!$lv_id) {
                        $ausgabe = '<p>' . __('Please enter a valid lecture ID.', 'rrze-univis') . '</p>';
                        break;
                    }
                    $this->controller->init($task, $shortcode_atts);
                    $ausgabe = $this->controller->ladeHTML();
                    break;
                case 'mitarbeiter-einzeln':
                    if (!($name || ($firstname && $lastname) || $univisid)) {
                        $ausgabe = '<p>' . __('Please enter a first and last name or a UnivIS ID.', 'rrze-univis') . '</p>';
                        break;
                    }
                    $this->controller->init($task, $shortcode_atts);
                    $ausgabe = $this->controller->ladeHTML();
                    break;
                default:
                    $ausgabe = $univis_link;
            }
        }
        return $ausgabe;
    }

    /*
     * Standard Shortcode-Attribute
     *
     * @return array
     */
    protected function default_atts()
    {
        $lang = get_locale();
        if (strpos($lang, 'en_') === 0) {
            $language = $this->set_language('_en');
        } else {
            $language = $this->set_language('');
        }

        $atts = [
            'UnivISOrgNr' => '0',
            'task' => 'mitarbeiter-alle',
            'personenanzeige_verzeichnis' => '',
            'personenanzeige_bildsuche' => 1,
            'personenanzeige_zusatzdatenInDatei' => 1,
            'personenanzeige_publikationen' => 1,
            'personenanzeige_lehrveranstaltungen' => 1,
            'lehrveranstaltung_verzeichnis' => '',
            'seiten_cache' => '0',
            'start_sommersemester' => '1.4',
            'start_wintersemester' => '1.10',
            'zeige_sprungmarken' => '',
            'orgunit' => '',
            'sortiere_alphabet' => '',
            'sortiere_jobs' => 1,
            'ignoriere_jobs' => [
                '_de' => 'Sicherheitsbeauftragter|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte',
                '_en' => 'Security commissary|IT-security commissary|Webmaster|Postmaster|IT-support|Local UnivIS administration',
            ],
            'zeige_jobs' => [],
            'datenverzeichnis' => '',
            'id' => '', // kann im Shortcode verwendet werden, sollte aber nicht
            'lv_id' => '', // Lehrveranstaltungs-ID
            'firstname' => '',
            'lastname' => '',
            'dozentid' => '', // ist im Shortcode ein Synonym zu univisid
            'dozentname' => '',
            'type' => '', // für Selektion nach Lehrveranstaltungstypen wie vorl
            'lv_import' => 1, // importierte Lehrveranstaltungen werden mit angezeigt, ausblenden über Shortcode
            'parent_lv' => 1, // Eltern-Lehrveranstaltungen werden mit angezeigt, ausblenden über Shortcode
            'sem' => '', // Semesterauswahl
            'univisid' => '', // ist die Personen-ID, egal ob dozentid oder MA-ID
            'name' => '', // Synonym zur Angabe von firstname und lastname
            'errormsg' => '', // Anzeige von Fehlermeldungen bei Ausgabe
            'lv_type' => 1, // Anzeige LV-Typ-Überschriften
            'lang' => $language, // wichtig für die Ausgabe englischer Bezeichnungen von orgunit, orgunits, text, description
            'leclanguage' => '', // Veranstaltungssprache
            'kompakt' => '', // Ausschließliche Anzeige LV-Überschriften
            'telefon' => 0, // optionale Anzeige von Telefonnumern bei den Mitarbeiter-Übersichten
            'mail' => 0, // optionale Anzeige von Mailadressen bei den Mitarbeiter-Übersichten
        ];

        return $atts;
    }

    private function set_language($lang)
    {
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
    
    public function mce_external_plugins()
    {
        if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
            add_filter('mce_external_languages', array($this, 'mce_languages'));
            add_filter('mce_external_plugins', array($this, 'mce_plugins'));
        }
    }

    public function mce_languages($locales)
    {
        $locales ['univis_shortcode'] = plugin_dir_path($this->plugin_file) . 'RRZE/UnivIS/MCE/langs.php';
        return $locales;
    }

    public function mce_plugins($plugin_array)
    {
        $min = defined('WP_DEBUG') && WP_DEBUG ? '' : '.min';
        $plugin_array['univis_shortcode'] = plugin_dir_url($this->plugin_file) . "RRZE/UnivIS/MCE/univis-shortcode$min.js";
        return $plugin_array;
    }
    
    public function endpoint_template_redirect()
    {
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
            $atts = [];

            foreach ($slugs as $k => $v) {
                $arr = explode('=', $v);
                $atts[$arr[0]] = $arr[1];
            }

            $this->controller->init($task, $atts);
            $data = $this->controller->ladeHTML();
        } else {
            $data = null;
        }

        $template = $this->locate_template();
        
        $this->load_template($template, $data);
        exit;
    }

    protected function locate_template()
    {
        $current_theme = wp_get_theme();
        $default_template = plugin_dir_path($this->plugin_file) . 'RRZE/UnivIS/Templates/single-univis.php';
        $template = '';
        
        foreach ($this->allowed_stylesheets as $theme => $style) {
            if (in_array(strtolower($current_theme->stylesheet), array_map('strtolower', $style))) {
                $template = plugin_dir_path($this->plugin_file) . "RRZE/UnivIS/Templates/Themes/$theme/single-univis.php";
                break;
            }
        }

        return !empty($template) && file_exists($template) ? $template : $default_template;
    }
    
    protected function load_template($template, $data = array())
    {
        include $template;
    }
}
