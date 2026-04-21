<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Config {
    private array $config = [];

    public function __construct() {
        $this->config = [
            'option_name' => 'rrze-univis',
            'constants' => [
                'defaults' => [
                    'univis_url' => 'https://univis.uni-erlangen.de',
                    'public_visibility_required_hosts' => 'uni-erlangen.de, fau.de',
                ],
                'fauthemes' => [
                    'FAU-Einrichtungen',
                    'FAU-Einrichtungen-BETA',
                    'FAU-Medfak',
                    'FAU-RWFak',
                    'FAU-Philfak',
                    'FAU-Techfak',
                    'FAU-Natfak',
                    'FAU-Blog',
                    'FAU-Jobs',
                ],
                'rrzethemes' => [
                    'RRZE 2019',
                ],
                'langcodes' => [
                    'de' => __('German', 'rrze-synonym'),
                    'en' => __('English', 'rrze-synonym'),
                    'es' => __('Spanish', 'rrze-synonym'),
                    'fr' => __('French', 'rrze-synonym'),
                    'ru' => __('Russian', 'rrze-synonym'),
                    'zh' => __('Chinese', 'rrze-synonym'),
                ],
                'endpoints' => [
                    'person' => 'univisid',
                    'lecture' => 'lv_id',
                ],
                'metabox' => [
                    'id' => 'get_univis_ids',
                    'posttypes' => ['post', 'page', 'faq', 'glossary', 'synonym'],
                    'context' => 'side',
                    'priority' => 'core',
                ],
                'search_types' => [
                    'departmentByName' => __('Organization', 'rrze-univis'),
                    'personByName' => __('Person', 'rrze-univis'),
                    'lectureByName' => __('Lecture', 'rrze-univis'),
                ],
                'ajax' => [
                    'search_action' => 'GetUnivISData',
                    'block_elements_action' => 'GetUnivISDataForBlockelements',
                    'nonce_action' => 'univis-ajax-nonce',
                    'nonce_name' => 'nonce',
                    'admin_script_handle' => 'rrze-unvis-ajax',
                    'admin_script_path' => 'js/rrze-univis.js',
                    'admin_script_object' => 'univis_ajax',
                ],
                'cache' => [
                    'transient_prefix' => 'rrze_univis_cache_',
                    'transient_jitter_minutes' => 30,
                    'transient_times' => [
                        'default' => 1440,
                        'persons' => 1440,
                        'departments' => 1440,
                        'publications' => 1440,
                        'lectures' => 720,
                        'positions' => 360,
                        'rooms' => 1440,
                    ],
                    'data_types' => [
                        'personByID' => 'persons',
                        'personByName' => 'persons',
                        'personAll' => 'departments',
                        'personByOrga' => 'persons',
                        'personByOrgaPhonebook' => 'persons',
                        'publicationByAuthorID' => 'publications',
                        'publicationByAuthor' => 'publications',
                        'publicationByDepartment' => 'publications',
                        'lectureByID' => 'lectures',
                        'lectureByDepartment' => 'lectures',
                        'lectureByLecturer' => 'lectures',
                        'lectureByLecturerID' => 'lectures',
                        'lectureByName' => 'lectures',
                        'jobByID' => 'positions',
                        'jobAll' => 'positions',
                        'roomByID' => 'rooms',
                        'roomByName' => 'rooms',
                        'departmentByName' => 'departments',
                        'departmentAll' => 'departments',
                    ],
                ],
                'dictionary_fields' => [
                    'title' => [
                        "Dr." => __('Doctor', 'rrze-univis'),
                        "Prof." => __('Professor', 'rrze-univis'),
                        "Dipl." => __('Diploma', 'rrze-univis'),
                        "Inf." => __('Computer Science', 'rrze-univis'),
                        "Wi." => __('Business Informatics', 'rrze-univis'),
                        "Ma." => __('Math', 'rrze-univis'),
                        "Ing." => __('Engineering', 'rrze-univis'),
                        "B.A." => __('Bachelor', 'rrze-univis'),
                        "M.A." => __('Magister Artium', 'rrze-univis'),
                        "phil." => __('Humanities', 'rrze-univis'),
                        "pol." => __('Political Science', 'rrze-univis'),
                        "nat." => __('Natural Science', 'rrze-univis'),
                        "soc." => __('Social Science', 'rrze-univis'),
                        "techn." => __('Technical Sciences', 'rrze-univis'),
                        "vet.med." => __('Veterinary Medicine', 'rrze-univis'),
                        "med.dent." => __('Dentistry', 'rrze-univis'),
                        "h.c." => __('honorary', 'rrze-univis'),
                        "med." => __('medicine', 'rrze-univis'),
                        "jur." => __('law', 'rrze-univis'),
                        "rer." => "",
                    ],
                    'lecture_type' => [
                        "awa" => __('Instructions for scientific work (AWA)', 'rrze-univis'),
                        "ku" => __('Course (KU)', 'rrze-univis'),
                        "ak" => __('Advanced course (AK)', 'rrze-univis'),
                        "ex" => __('Excursion (EX)', 'rrze-univis'),
                        "gk" => __('Basic course (GK)', 'rrze-univis'),
                        "sem" => __('Seminar (SEM)', 'rrze-univis'),
                        "es" => __('Exam seminar (ES)', 'rrze-univis'),
                        "ts" => __('Theory Seminar (TS)', 'rrze-univis'),
                        "ag" => __('Working group (AG)', 'rrze-univis'),
                        "mas" => __('Master seminar (MAS)', 'rrze-univis'),
                        "gs" => __('Basic seminar (GS)', 'rrze-univis'),
                        "us" => __('Training seminar (US)', 'rrze-univis'),
                        "as" => __('Advanced seminar (AS)', 'rrze-univis'),
                        "hs" => __('Main seminar (HS)', 'rrze-univis'),
                        "re" => __('Repetitorium (RE)', 'rrze-univis'),
                        "kk" => __('Exam course (KK)', 'rrze-univis'),
                        "klv" => __('Clinical visit (KLV)', 'rrze-univis'),
                        "ko" => __('Colloquium (KO)', 'rrze-univis'),
                        "ks" => __('Combined seminar (KS)', 'rrze-univis'),
                        "ek" => __('Introductory course (EK)', 'rrze-univis'),
                        "ms" => __('Middle seminar (MS)', 'rrze-univis'),
                        "os" => __('Upper seminar (OS)', 'rrze-univis'),
                        "pr" => __('Internship (PR)', 'rrze-univis'),
                        "prs" => __('Practice seminar (PRS)', 'rrze-univis'),
                        "pjs" => __('Project Seminar (PJS)', 'rrze-univis'),
                        "ps" => __('Pro seminar (PS)', 'rrze-univis'),
                        "sl" => __('Other courses (SL)', 'rrze-univis'),
                        "tut" => __('Tutorial (TUT)', 'rrze-univis'),
                        "v-ue" => __('Lecture with exercise (V/UE)', 'rrze-univis'),
                        "ue" => __('Exercise (UE)', 'rrze-univis'),
                        "vorl" => __('Lecture (VORL)', 'rrze-univis'),
                        "hvl" => __('Main Lecture (HVL)', 'rrze-univis'),
                        "pf" => __('Examination (PF)', 'rrze-univis'),
                        "gsz" => __('Committee meeting (GSZ)', 'rrze-univis'),
                        "ppu" => __('Propaedeutic Exercise (PPU)', 'rrze-univis'),
                        "his" => __('History of Languages Seminar (HIS)', 'rrze-univis'),
                        "bsem" => __('Accompanying seminar (BSEM)', 'rrze-univis'),
                        "kol" => __('College (KOL)', 'rrze-univis'),
                        "mhs" => __('MS (HS, PO 2020) (MHS)', 'rrze-univis'),
                        "pgmas" => __('PG Master Seminar (PGMAS)', 'rrze-univis'),
                        "pms" => __('PS (MS, PO 2020) (PMS)', 'rrze-univis'),
                    ],
                    'repeat' => [
                        "w1" => "",
                        "w2" => __('Every other week', 'rrze-univis'),
                        "w3" => __('Every third week', 'rrze-univis'),
                        "w4" => __('Every fourth week', 'rrze-univis'),
                        "w5" => "",
                        "m1" => "",
                        "s1" => __('single appointment on', 'rrze-univis'),
                        "bd" => __('block event', 'rrze-univis'),
                        '0' => __(' Sun', 'rrze-univis'),
                        '1' => __(' Mon', 'rrze-univis'),
                        '2' => __(' Tue', 'rrze-univis'),
                        '3' => __(' Wed', 'rrze-univis'),
                        '4' => __(' Thu', 'rrze-univis'),
                        '5' => __(' Fry', 'rrze-univis'),
                        '6' => __(' Sat', 'rrze-univis'),
                        '7' => __(' Sun', 'rrze-univis'),
                    ],
                    'publication_type' => [
                        "artmono" => __('Article in anthology', 'rrze-univis'),
                        "arttagu" => __('Article in proceedings', 'rrze-univis'),
                        "artzeit" => __('Article in magazine', 'rrze-univis'),
                        "techrep" => __('Internal Report (Technical Report, Research Report)', 'rrze-univis'),
                        "hschri" => __('University thesis (dissertation, habilitation thesis, diploma thesis etc.)', 'rrze-univis'),
                        "dissvg" => __('Thesis (also published by the publisher)', 'rrze-univis'),
                        "monogr" => __('Monograph', 'rrze-univis'),
                        "tagband" => __('Conference volume (not published by the publisher)', 'rrze-univis'),
                        "schutzr" => __('IPR', 'rrze-univis'),
                    ],
                    'hstype' => [
                        "diss" => __('Dissertation', 'rrze-univis'),
                        "dipl" => __('Diploma', 'rrze-univis'),
                        "mag" => __('Master\'s thesis', 'rrze-univis'),
                        "stud" => __('Study paper', 'rrze-univis'),
                        "habil" => __('Habilitation thesis', 'rrze-univis'),
                        "masth" => __('Master\'s thesis', 'rrze-univis'),
                        "bacth" => __('Bachelor thesis', 'rrze-univis'),
                        "intber" => __('Internal Report', 'rrze-univis'),
                        "diskus" => __('Discussion paper', 'rrze-univis'),
                        "discus" => __('Discussion paper', 'rrze-univis'),
                        "forber" => __('Research report', 'rrze-univis'),
                        "absber" => __('Final report', 'rrze-univis'),
                        "patschri" => __('Patent specification', 'rrze-univis'),
                        "offenleg" => __('Disclosure document', 'rrze-univis'),
                        "patanmel" => __('Patent application', 'rrze-univis'),
                        "gebrmust" => __('Utility model', 'rrze-univis'),
                    ],
                    'leclanguage' => [
                        0 => __('Lecture\'s language German', 'rrze-univis'),
                        "D" => __('Lecture\'s language German', 'rrze-univis'),
                        "E" => __('Lecture\'s language English', 'rrze-univis'),
                    ],
                    'sws' => __(' SWS', 'rrze-univis'),
                    'schein' => __('Certificate', 'rrze-univis'),
                    'ects' => __('ECTS studies', 'rrze-univis'),
                    'ects_cred' => __('ECTS credits: ', 'rrze-univis'),
                    'beginners' => __('Suitable for beginners', 'rrze-univis'),
                    'fruehstud' => __('Early study', 'rrze-univis'),
                    'gast' => __('Allowed for guest students', 'rrze-univis'),
                    'evaluation' => __('Evaluation', 'rrze-univis'),
                    'locations' => '',
                    'organizational' => '',
                ],
                'widget' => [
                    'id_base' => 'univis_widget',
                    'description' => __('Displays a lecture, person or publication', 'rrze-univis'),
                    'tasks' => [
                        'lehrveranstaltungen-einzeln' => __('Lecture', 'rrze-univis'),
                        'mitarbeiter-einzeln' => __('Person', 'rrze-univis'),
                    ],
                    'task_field_map' => [
                        'lehrveranstaltungen-einzeln' => 'lv_id',
                        'default' => 'univisid',
                    ],
                ],
            ],
            'menu_settings' => [
                'page_title' => __('RRZE UnivIS', 'rrze-univis'),
                'menu_title' => __('RRZE UnivIS', 'rrze-univis'),
                'capability' => 'manage_options',
                'menu_slug' => 'rrze-univis',
                'title' => __('RRZE UnivIS Settings', 'rrze-univis'),
            ],
            'help_tab' => [
                [
                    'id' => 'rrze-univis-help',
                    'content' => [
                        '<p>' . __('Here comes the Context Help content.', 'rrze-univis') . '</p>',
                    ],
                    'title' => __('Overview', 'rrze-univis'),
                    'sidebar' => sprintf(
                        '<p><strong>%1$s:</strong></p><p><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></p><p><a href="https://github.com/RRZE Webteam">%2$s</a></p>',
                        __('For more information', 'rrze-univis'),
                        __('RRZE Webteam on Github', 'rrze-univis')
                    ),
                ],
            ],
            'sections' => [
                [
                    'id' => 'basic',
                    'title' => __('UnivIS Settings', 'rrze-univis'),
                ],
            ],
            'shortcode_settings' => [
                'mitarbeiter' => [
                    'block' => [
                        'blocktype' => 'rrze-univis/univismitarbeiter',
                        'blockname' => 'univismitarbeiter',
                        'title' => 'RRZE-UnivIS Mitarbeiter',
                        'category' => 'widgets',
                        'icon' => 'admin-users',
                        'tinymce_icon' => 'user',
                    ],
                    'task' => [
                        'values' => [
                            ['id' => 'mitarbeiter-einzeln', 'val' => __('Single employee', 'rrze-univis')],
                            ['id' => 'mitarbeiter-alle', 'val' => __('All employees', 'rrze-univis')],
                            ['id' => 'mitarbeiter-telefonbuch', 'val' => __('Employees phone book', 'rrze-univis')],
                            ['id' => 'mitarbeiter-orga', 'val' => __('Employees organization', 'rrze-univis')],
                        ],
                        'default' => 'mitarbeiter-alle',
                        'field_type' => 'select',
                        'label' => __('Please select', 'rrze-univis'),
                        'type' => 'string',
                    ],
                    'name' => ['default' => '', 'field_type' => 'text', 'label' => __('Last name, first name', 'rrze-univis'), 'type' => 'string'],
                    'univisid' => ['default' => '', 'field_type' => 'text', 'label' => __('UnivIS ID person', 'rrze-univis'), 'type' => 'string'],
                    'show' => ['default' => '', 'field_type' => 'text', 'label' => __('show', 'rrze-univis'), 'type' => 'string'],
                    'hide' => ['default' => '', 'field_type' => 'text', 'label' => __('hide', 'rrze-univis'), 'type' => 'string'],
                    'ignoriere_jobs' => ['default' => 'Sicherheitsbeauftragter|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte|Security commissary|IT-security commissary|Local UnivIS administration', 'field_type' => 'text', 'label' => __('Ignore jobs - separate individual activities with | from each other.', 'rrze-univis'), 'type' => 'string'],
                    'zeige_jobs' => ['default' => '', 'field_type' => 'text', 'label' => __('Show these jobs only', 'rrze-univis'), 'type' => 'string'],
                    'call' => ['field_type' => 'toggle', 'label' => __('Make phone numbers dialable', 'rrze-univis'), 'type' => 'boolean', 'default' => true, 'checked' => true],
                    'number' => ['field_type' => 'text', 'label' => __('UnivIS OrgID', 'rrze-univis'), 'default' => '', 'type' => 'string'],
                    'hstart' => ['default' => 2, 'field_type' => 'text', 'label' => __('Size of heading where output starts', 'rrze-univis'), 'type' => 'number'],
                    'show_phone' => ['field_type' => 'toggle', 'label' => __('Show phone numbers', 'rrze-univis'), 'type' => 'boolean', 'default' => true, 'checked' => true],
                    'show_mail' => ['field_type' => 'toggle', 'label' => __('Show eMail', 'rrze-univis'), 'type' => 'boolean', 'default' => true, 'checked' => true],
                    'show_jumpmarks' => ['field_type' => 'toggle', 'label' => __('Show anchors', 'rrze-univis'), 'type' => 'boolean', 'default' => true, 'checked' => true],
                    'lang' => ['default' => '', 'field_type' => 'text', 'label' => __('Language', 'rrze-univis'), 'type' => 'string'],
                ],
                'lehrveranstaltungen' => [
                    'block' => [
                        'blocktype' => 'rrze-univis/univislehrveranstaltungen',
                        'blockname' => 'univislehrveranstaltungen',
                        'title' => 'RRZE-UnivIS Lehrveranstaltungen',
                        'category' => 'widgets',
                        'icon' => 'bank',
                        'tinymce_icon' => 'paste',
                    ],
                    'task' => [
                        'values' => [
                            ['id' => 'lehrveranstaltungen-einzeln', 'val' => __('Single lecture', 'rrze-univis')],
                            ['id' => 'lehrveranstaltungen-alle', 'val' => __('All lectures', 'rrze-univis')],
                        ],
                        'default' => 'lehrveranstaltungen-alle',
                        'field_type' => 'select',
                        'label' => __('Please select', 'rrze-univis'),
                        'type' => 'string',
                    ],
                    'id' => ['default' => '', 'field_type' => 'text', 'label' => __('UnivIS ID lecture', 'rrze-univis'), 'type' => 'string'],
                    'name' => ['default' => '', 'field_type' => 'text', 'label' => __('Last name, first name', 'rrze-univis'), 'type' => 'string'],
                    'univisid' => ['default' => '', 'field_type' => 'text', 'label' => __('UnivIS ID person', 'rrze-univis'), 'type' => 'string'],
                    'dozentid' => ['default' => '', 'field_type' => 'text', 'label' => __('UnivIS ID person', 'rrze-univis'), 'type' => 'string'],
                    'lv_import' => ['field_type' => 'toggle', 'label' => __('Output imported courses', 'rrze-univis'), 'type' => 'boolean', 'default' => false, 'checked' => false],
                    'type' => ['default' => '', 'field_type' => 'text', 'label' => __('Type f.e. vorl (=lecture)', 'rrze-univis'), 'type' => 'string'],
                    'order' => ['default' => '', 'field_type' => 'text', 'label' => __('Sort by type f.e. "vorl,ueb"', 'rrze-univis'), 'type' => 'string'],
                    'sem' => ['default' => '', 'field_type' => 'text', 'label' => __('Semester f.e. 2020w', 'rrze-univis'), 'type' => 'string'],
                    'sprache' => ['default' => '', 'field_type' => 'text', 'label' => __('language', 'rrze-univis'), 'type' => 'string'],
                    'number' => ['default' => '', 'field_type' => 'text', 'label' => __('UnivIS OrgID', 'rrze-univis'), 'type' => 'string'],
                    'show' => ['default' => '', 'field_type' => 'text', 'label' => __('show', 'rrze-univis'), 'type' => 'string'],
                    'hide' => ['default' => '', 'field_type' => 'text', 'label' => __('hide', 'rrze-univis'), 'type' => 'string'],
                    'hstart' => ['default' => 2, 'field_type' => 'text', 'label' => __('Size of heading where output starts', 'fau-person'), 'type' => 'number'],
                    'fruehstud' => ['field_type' => 'toggle', 'label' => __('Show early studies only', 'rrze-univis'), 'type' => 'boolean', 'default' => null, 'checked' => false],
                    'gast' => ['field_type' => 'toggle', 'label' => __('Show suitable for visiting students only', 'rrze-univis'), 'type' => 'boolean', 'default' => null, 'checked' => false],
                ],
                'publikationen' => [
                    'block' => [
                        'blocktype' => 'rrze-univis/univispublikationen',
                        'blockname' => 'univispublikationen',
                        'title' => 'RRZE-UnivIS Publikationen',
                        'category' => 'widgets',
                        'icon' => 'megaphone',
                        'tinymce_icon' => 'preview',
                    ],
                    'task' => [
                        'values' => [
                            ['id' => 'publikationen', 'val' => __('Publications', 'rrze-univis')],
                        ],
                        'default' => 'publikationen',
                        'field_type' => 'select',
                        'label' => __('Please select', 'rrze-univis'),
                        'type' => 'string',
                    ],
                    'name' => ['default' => '', 'field_type' => 'text', 'label' => __('Last name, first name', 'rrze-univis'), 'type' => 'string'],
                    'univisid' => ['default' => '', 'field_type' => 'text', 'label' => __('UnivIS ID person', 'rrze-univis'), 'type' => 'string'],
                    'since' => ['default' => 0, 'field_type' => 'text', 'label' => __('Show from the specified year of publication. F.e. 2017', 'rrze-univis'), 'type' => 'number'],
                    'number' => ['default' => '', 'field_type' => 'text', 'label' => __('UnivIS OrgID', 'rrze-univis'), 'type' => 'string'],
                    'hstart' => ['default' => 2, 'field_type' => 'text', 'label' => __('Size of heading where output starts', 'fau-person'), 'type' => 'number'],
                ],
            ],
        ];
    }

    public function get(string $key, mixed $default = null): mixed {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function set(string $key, mixed $newValue): void {
        $segments = explode('.', $key);
        $value = &$this->config;

        foreach ($segments as $segment) {
            if (!isset($value[$segment]) || !is_array($value[$segment])) {
                $value[$segment] = [];
            }

            $value = &$value[$segment];
        }

        $value = $newValue;
    }

    public function getOptionName(): string {
        return $this->get('option_name', 'rrze-univis');
    }

    public function getConstants(): array {
        return $this->get('constants', []);
    }

    public function getMenuSettings(): array {
        return $this->get('menu_settings', []);
    }

    public function getHelpTab(): array {
        return $this->get('help_tab', []);
    }

    public function getSections(): array {
        return $this->get('sections', []);
    }

    public function getFields(): array {
        return [
            'basic' => [
                ['name' => 'univis_url', 'label' => __('Link to <b><i>Univ</i>IS</b>', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => __('', 'rrze-univis'), 'type' => 'text', 'default' => $this->get('constants.defaults.univis_url'), 'sanitize_callback' => 'sanitize_url'],
                ['name' => 'univis_linktxt', 'label' => __('Text to <b><i>Univ</i>IS</b> link', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => __('', 'rrze-univis'), 'type' => 'text', 'default' => __('<b><i>Univ</i>IS</b> - Information System of the FAU', 'rrze-univis'), 'sanitize_callback' => 'sanitize_text_field'],
                ['name' => 'UnivISOrgNr', 'label' => __('<b><i>Univ</i>IS</b> OrgNr.', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => '', 'type' => 'text', 'default' => '', 'sanitize_callback' => 'sanitize_text_field'],
                ['name' => 'public_visiblity_required_hosts', 'label' => __('Required hosts for nonpublic data', 'rrze-jobs'), 'desc' => __('Nonpublic persons\'s data will be displayed only on hosts from the given hostnames', 'rrze-jobs'), 'type' => 'textarea', 'default' => $this->get('constants.defaults.public_visibility_required_hosts')],
                ['name' => 'semesterMin', 'label' => __('Find Lectures from the soúmmer semester', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => '', 'min' => 0, 'max' => 99999999999, 'step' => '1', 'type' => 'number', 'default' => date('Y') - 1, 'sanitize_callback' => 'floatval'],
                ['name' => 'wsStart', 'label' => __('Lectures begin this winter semester', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => '', 'type' => 'date', 'default' => date('Y') - 1 . '-11-02', 'sanitize_callback' => 'date'],
                ['name' => 'wsEnd', 'label' => __('End of the lecture period this winter semester', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => '', 'type' => 'date', 'default' => date('Y') . '-02-12', 'sanitize_callback' => 'date'],
                ['name' => 'ssStart', 'label' => __('Lectures begin this summer semester', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => '', 'type' => 'date', 'default' => date('Y') . '-04-12', 'sanitize_callback' => 'date'],
                ['name' => 'ssEnd', 'label' => __('End of the lecture period this summer semester', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'placeholder' => '', 'type' => 'date', 'default' => date('Y') . '-07-16', 'sanitize_callback' => 'date'],
                ['name' => 'hstart', 'label' => __('Size of heading where output starts', 'rrze-univis'), 'desc' => __('', 'rrze-univis'), 'min' => 2, 'max' => 10, 'step' => '1', 'type' => 'number', 'default' => '2', 'sanitize_callback' => 'floatval'],
                ['name' => 'enable_metabox', 'label' => __('Enable UnivIS ID metabox', 'rrze-univis'), 'desc' => __('Enable the legacy UnivIS ID search metabox in the editor.', 'rrze-univis'), 'type' => 'checkbox', 'default' => false],
                ['name' => 'enable_widgets', 'label' => __('Enable classic widgets', 'rrze-univis'), 'desc' => __('Enable the legacy UnivIS widget for classic themes or classic widget areas.', 'rrze-univis'), 'type' => 'checkbox', 'default' => false],
            ],
        ];
    }

    public function getShortcodeSettings(): array {
        return $this->get('shortcode_settings', []);
    }
}
