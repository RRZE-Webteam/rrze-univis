<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

/**
 * Shortcode
 */
class Shortcode {
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $plugin;
    protected $UnivISOrgNr;
    protected $UnivISURL;
    protected $UnivISLink;
    protected $options;
    protected $show = [];
    protected $hide = [];
    protected $atts;
    protected $cache;
    protected $template;
    protected $noCache = false;
    private $shortcodeSettings = '';
    private $config;
    private $taskMap = [
        'mitarbeiter-einzeln' => ['settings' => 'mitarbeiter', 'handler' => 'getSingleEmployeeData'],
        'mitarbeiter-orga' => ['settings' => 'mitarbeiter', 'handler' => 'getEmployeeOrganizationData'],
        'mitarbeiter-telefonbuch' => ['settings' => 'mitarbeiter', 'handler' => 'getEmployeePhonebookData'],
        'mitarbeiter-alle' => ['settings' => 'mitarbeiter', 'handler' => 'getAllEmployeesData'],
        'lehrveranstaltungen-einzeln' => ['settings' => 'lehrveranstaltungen', 'handler' => 'getSingleLectureData'],
        'lehrveranstaltungen-alle' => ['settings' => 'lehrveranstaltungen', 'handler' => 'getAllLecturesData'],
        'publikationen' => ['settings' => 'publikationen', 'handler' => 'getPublicationData'],
    ];

    /**
     * Variablen Werte zuweisen.
     * @param Plugin $plugin Plugin object
     */
    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $this->config = new Config();
        $this->template = new Template($this->config, $this->plugin->getPath('templates'));
        $this->shortcodeSettings = $this->config->getShortcodeSettings();
        $this->options = get_option('rrze-univis');
        $constants = $this->config->getConstants();
        $this->UnivISOrgNr = (!empty($this->options['basic_UnivISOrgNr']) ? $this->options['basic_UnivISOrgNr'] : 0);
        $this->UnivISURL = (!empty($this->options['basic_univis_url']) ? $this->options['basic_univis_url'] : $constants['defaults']['univis_url']);
        $this->UnivISLink = sprintf('<a href="%1$s">%2$s</a>', $this->UnivISURL, (!empty($this->options['basic_univis_linktxt']) ? $this->options['basic_univis_linktxt'] : __('Text for UnivIS link is missing', 'rrze-univis')));
        add_action('init', [$this, 'initGutenberg']);
    }

    /**
     * Er wird ausgeführt, sobald die Klasse instanziiert wird.
     * @return void
     */
    public function onLoaded(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('univis', [$this, 'shortcodeOutput']);
    }

    public function enqueueScripts(): void {
        wp_register_style('rrze-univis', $this->plugin->getUrl('css') . 'rrze-univis.css');
        wp_enqueue_style('rrze-univis');
    }

    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput(mixed $atts): string {
        $this->shortcodeSettings = $this->config->getShortcodeSettings();
        $this->noCache = is_array($atts) && !empty($atts['nocache']);

        if (empty($atts)) {
            return $this->UnivISLink;
        }

        if (!is_array($atts)) {
            return '';
        }

        // lv_id is not in config (=> id)
        if (!empty($atts['lv_id'])) {
            $atts['id'] = $atts['lv_id'];
            if ($atts['task'] == 'lehrveranstaltungen-alle') {
                $atts['task'] = 'lehrveranstaltungen-einzeln';
            }
        }

        if (empty($atts['task'])) {
            $atts['task'] = 'mitarbeiter-alle';
        }

        if (empty($this->taskMap[$atts['task']])) {
            return '';
        }

        $taskConfig = $this->taskMap[$atts['task']];
        $this->shortcodeSettings = $this->shortcodeSettings[$taskConfig['settings']];

        // merge given attributes with default ones
        $atts_default = array();
        foreach ($this->shortcodeSettings as $k => $v) {
            if ($k != 'block') {
                $atts_default[$k] = $v['default'];
            }
        }

        $this->atts = $this->normalize(shortcode_atts($atts_default, $atts));

        $data = '';

        $this->cache = new Cache($this->UnivISURL, $this->UnivISOrgNr, $this->atts, $this->noCache);
        $data = $this->{$taskConfig['handler']}($atts);
        $person = $this->getTemplatePerson($data);
        $lecture = $this->getTemplateLecture($data);

        if ($data && is_array($data)) {
            return str_replace("\n", " ", $this->template->render($this->atts['task'], [
                'data' => $data,
                'person' => $person,
                'lecture' => $lecture,
            ], $this));
        } else {
            return __('No matching records found.', 'rrze-univis'); // Keine passenden Datensätze gefunden.
        }
    }

    private function getTemplatePerson(mixed $data): ?array {
        if ($this->atts['task'] != 'mitarbeiter-einzeln' || empty($data[0])) {
            return null;
        }

        return $data[0];
    }

    private function getTemplateLecture(mixed $data): ?array {
        if ($this->atts['task'] != 'lehrveranstaltungen-einzeln' || empty($data) || !is_array($data)) {
            return null;
        }

        return $data[array_key_first($data)][0] ?? null;
    }

    private function ensureVisible(string $field): void {
        if (!in_array($field, $this->hide) && !in_array($field, $this->show)) {
            $this->show[] = $field;
        }
    }

    private function getSingleEmployeeData(array $atts): mixed {
        $this->ensureVisible('telefon');
        $this->ensureVisible('mail');
        $data = '';

        if (!empty($atts['univisid'])) {
            $data = $this->cache->getData('personByID', $this->atts['univisid']);
            if ($data) {
                $this->atts['name'] = $data[0]['lastname'] . ',' . $data[0]['firstname'];
            }
        } elseif (!empty($this->atts['name'])) {
            $data = $this->cache->getData('personByName', $this->atts['name']);
        }

        if ($data && !empty($this->atts['name'])) {
            $data[0]['lectures'] = $this->cache->getData('lectureByLecturer', $this->atts['name']);
        }

        return $data;
    }

    private function getEmployeeOrganizationData(array $atts): mixed {
        return $this->cache->getData('personByOrga');
    }

    private function getEmployeePhonebookData(array $atts): mixed {
        return $this->cache->getData('personByOrgaPhonebook');
    }

    private function getAllEmployeesData(array $atts): mixed {
        $this->ensureVisible('telefon');
        return $this->cache->getData('personAll', null);
    }

    private function getSingleLectureData(array $atts): mixed {
        $data = '';

        if (!empty($this->atts['id'])) {
            $data = $this->cache->getData('lectureByID', $this->atts['id']);
        } elseif (!empty($this->atts['name'])) {
            $data = $this->cache->getData('lectureByLecturer', $this->atts['name']);
        } elseif (!empty($this->atts['univisid'])) {
            $data = $this->cache->getData('lectureByLecturerID', $this->atts['univisid']);
        }

        return $data;
    }

    private function getAllLecturesData(array $atts): mixed {
        if (!empty($this->atts['name'])) {
            return $this->cache->getData('lectureByLecturer', $this->atts['name']);
        }

        if (!empty($this->atts['univisid'])) {
            return $this->cache->getData('lectureByLecturerID', $this->atts['univisid']);
        }

        if (!empty($this->atts['id'])) {
            return $this->cache->getData('lectureByLecturerID', $this->atts['id']);
        }

        return $this->cache->getData('lectureByDepartment');
    }

    private function getPublicationData(array $atts): mixed {
        if (!empty($atts['name'])) {
            return $this->cache->getData('publicationByAuthor', $this->atts['name']);
        }

        if (!empty($this->atts['univisid'])) {
            return $this->cache->getData('publicationByAuthorID', $this->atts['univisid']);
        }

        return $this->cache->getData('publicationByDepartment');
    }

    public function normalize(array $atts): array {
        // normalize given attributes according to rrze-univis version 2
        if (!empty($atts['number'])) {
            $this->UnivISOrgNr = $atts['number'];
        } elseif (!empty($atts['task']) && ($atts['task'] == 'lehrveranstaltungen-alle' || $atts['task'] == 'mitarbeiter-einzeln') && !empty($atts['id'])) {
            $this->UnivISOrgNr = $atts['id'];
        }
        if (!empty($atts['dozentid'])) {
            $atts['id'] = $atts['dozentid'];
        }
        if (!empty($atts['dozentname'])) {
            $atts['name'] = $atts['dozentname'];
        }
        if (empty($atts['show'])) {
            $atts['show'] = '';
        }
        if (empty($atts['hide'])) {
            $atts['hide'] = '';
        }
        if (!empty($atts['sprache'])) {
            $atts['lang'] = $atts['sprache'];
        }
        if (isset($atts['show_phone'])) {
            if ($atts['show_phone']) {
                $atts['show'] .= ',telefon';
            } else {
                $atts['hide'] .= ',telefon';
            }
        }
        if (isset($atts['show_mail'])) {
            if ($atts['show_mail']) {
                $atts['show'] .= ',mail';
            } else {
                $atts['hide'] .= ',mail';
            }
        }
        if (isset($atts['show_jumpmarks'])) {
            if ($atts['show_jumpmarks']) {
                $atts['show'] .= ',sprungmarken';
            } else {
                $atts['hide'] .= ',sprungmarken';
            }
        }
      
        if (isset($atts['call'])) {
            if ($atts['call']) {
                $atts['show'] .= ',call';
            } else {
                $atts['hide'] .= ',call';
            }
        }
        if (!empty($atts['show'])) {
            $this->show = array_map('trim', explode(',', strtolower($atts['show'])));
        }
        if (!empty($atts['hide'])) {
            $this->hide = array_map('trim', explode(',', strtolower($atts['hide'])));
        }
        if (!empty($atts['sem'])) {
            if (is_int($atts['sem'])) {
                $year = date("Y") + $atts['sem'];
                $thisSeason = (in_array(date('n'), [10, 11, 12, 1]) ? 'w' : 's');
                $season = ($thisSeason === 's' ? 'w' : 's');
                $atts['sem'] = $year . $season;
            }
        }
        if (empty($atts['hstart'])) {
            $atts['hstart'] = $this->options['basic_hstart'];
        }

        return $atts;
    }

    public function isGutenberg(): bool {
        $postID = get_the_ID();
        if ($postID && !use_block_editor_for_post($postID)) {
            return false;
        }
        return true;
    }

    private function makeDropdown(string $id, string $label, array $aData, ?string $all = null): array {
        $ret = [
            'id' => $id,
            'label' => $label,
            'field_type' => 'select',
            'default' => '',
            'type' => 'string',
            'items' => ['type' => 'text'],
            'values' => [['id' => '', 'val' => (empty($all) ? __('-- All --', 'rrze-univis') : $all)]],
        ];

        foreach ($aData as $id => $name) {
            $ret['values'][] = [
                'id' => $id,
                'val' => htmlspecialchars(str_replace('"', "", str_replace("'", "", $name)), ENT_QUOTES, 'UTF-8'),
            ];
        }

        return $ret;
    }

    private function makeToggle(string $label): array {
        return [
            'label' => $label,
            'field_type' => 'toggle',
            'default' => true,
            'checked' => true,
            'type' => 'boolean',
        ];
    }

    public function fillGutenbergOptions(array $aSettings): array {
        $this->cache = new Cache($this->UnivISURL, $this->UnivISOrgNr, null);

        foreach ($aSettings as $task => $settings) {
            $settings['number']['default'] = $this->UnivISOrgNr;

            // Mitarbeiter
            if (isset($settings['name'])) {
                unset($settings['name']);
                if ($task != 'lehrveranstaltungen') {
                    unset($settings['id']);
                }
                $aPersons = [];
                $data = $this->cache->getData('personAll');
                if (is_array($data)) {
                    foreach ($data as $position => $persons) {
                        foreach ($persons as $person) {
                            $aPersons[$person['person_id']] = $person['lastname'] . (!empty($person['firstname']) ? ', ' . $person['firstname'] : '');
                        }
                    }
                }
                asort($aPersons);
                $settings['univisid'] = $this->makeDropdown('univisid', __('Person', 'rrze-univis'), $aPersons);

            }

            // Lehrveranstaltungen
            if (isset($settings['id'])) {
                $aLectures = [];
                $aLectureTypes = [];
                $aLectureLanguages = [];
                $data = $this->cache->getData('lectureByDepartment');

                if (is_array($data)) {
                    foreach ($data as $type => $lecs) {
                        foreach ($lecs as $lecture) {
                            $aLectureTypes[$lecture['lecture_type']] = $type;
                            if (!empty($lecture['leclanguage_long'])) {
                                $parts = explode(' ', $lecture['leclanguage_long']);
                                $aLectureLanguages[$lecture['leclanguage']] = $parts[1] ?? $lecture['leclanguage_long'];
                            }
                            $aLectures[$lecture['lecture_id']] = $lecture['name'];
                        }
                    }
                }

                asort($aLectures);
                $settings['id'] = $this->makeDropdown('id', __('Lecture', 'rrze-univis'), $aLectures);

                asort($aLectureTypes);
                $settings['type'] = $this->makeDropdown('type', __('Type', 'rrze-univis'), $aLectureTypes);

                asort($aLectureLanguages);
                $settings['sprache'] = $this->makeDropdown('sprache', __('Language', 'rrze-univis'), $aLectureLanguages);

                // Semester
                if (isset($settings['sem'])) {
                    $settings['sem'] = $this->makeDropdown('sem', __('Semester', 'rrze-univis'), [], __('-- Current semester --', 'rrze-univis'));
                    $thisSeason = (in_array(date('n'), [10, 11, 12, 1]) ? 'w' : 's');
                    $season = ($thisSeason === 's' ? 'w' : 's');
                    $nextYear = date("Y") + 1;
                    $settings['sem']['values'][] = ['id' => $nextYear . $season, 'val' => $nextYear . $season];
                    $lastYear = $nextYear - 2;
                    $settings['sem']['values'][] = ['id' => $lastYear . $season, 'val' => $lastYear . $season];

                    $minYear = (!empty($this->options['basic_semesterMin']) ? $this->options['basic_semesterMin'] : 1971);
                    for ($i = date("Y"); $i >= $minYear; $i--) {
                        $settings['sem']['values'][] = ['id' => $i . 's', 'val' => $i . ' ' . __('SS', 'rrze-univis')];
                        $settings['sem']['values'][] = ['id' => $i . 'w', 'val' => $i . ' ' . __('WS', 'rrze-univis')];
                    }
                }

                unset($settings['dozentid']);
            }

            // 2DO: we need document ready() or equal on React built elements to use onChange of UnivIS Org Nr. to refill dropdowns
            // unset($settings['number']);
            unset($settings['show']);
            unset($settings['hide']);

            $aSettings[$task] = $settings;
        }
        return $aSettings;
    }

    public function initGutenberg(): void {
        $editorScript = 'rrze-univis-blocksupport';

        if (!$this->isGutenberg() || empty($this->UnivISURL) || empty($this->UnivISOrgNr)) {
            return;
        }
        // get prefills for dropdowns
        $aSettings = $this->fillGutenbergOptions($this->shortcodeSettings);

        wp_register_script(
            $editorScript,
            $this->plugin->getUrl('js') . 'rrze-univis-blocksupport.js',
            array(
                'jquery',
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor',
                'wp-server-side-render',
            ),
            null
        );

        wp_localize_script($editorScript, 'rrzeUnivisBlockConfigs', $aSettings);

        foreach ($aSettings as $task => $settings) {
            // register block
            register_block_type($settings['block']['blocktype'], array(
                'editor_script' => $editorScript,
                'render_callback' => [$this, 'shortcodeOutput'],
                'attributes' => $this->getBlockAttributes($settings),
            )
            );
        }
    }

    private function getBlockAttributes(array $settings): array {
        $attributes = [];

        foreach ($settings as $name => $setting) {
            if ($name === 'block' || !is_array($setting) || empty($setting['type'])) {
                continue;
            }

            $attributes[$name] = [
                'type' => $setting['type'],
            ];

            if (array_key_exists('default', $setting) && $setting['default'] !== null) {
                $attributes[$name]['default'] = $setting['default'];
            }
        }

        return $attributes;
    }

}
