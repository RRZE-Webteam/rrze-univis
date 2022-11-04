<?php

namespace RRZE\UnivIS\Config;

defined('ABSPATH') || exit;

/**
 * Gibt der Name der Option zurück.
 * @return array [description]
 */
function getOptionName()
{
    return 'rrze-univis';
}

function getConstants()
{
    $options = array(
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
            "de" => __('German', 'rrze-synonym'),
            "en" => __('English', 'rrze-synonym'),
            "es" => __('Spanish', 'rrze-synonym'),
            "fr" => __('French', 'rrze-synonym'),
            "ru" => __('Russian', 'rrze-synonym'),
            "zh" => __('Chinese', 'rrze-synonym'),
        ],
    );
    return $options;
}

/**
 * Gibt die Einstellungen des Menus zurück.
 * @return array [description]
 */
function getMenuSettings()
{
    return [
        'page_title' => __('RRZE UnivIS', 'rrze-univis'),
        'menu_title' => __('RRZE UnivIS', 'rrze-univis'),
        'capability' => 'manage_options',
        'menu_slug' => 'rrze-univis',
        'title' => __('RRZE UnivIS Settings', 'rrze-univis'),
    ];
}

/**
 * Gibt die Einstellungen der Inhaltshilfe zurück.
 * @return array [description]
 */
function getHelpTab()
{
    return [
        [
            'id' => 'rrze-univis-help',
            'content' => [
                '<p>' . __('Here comes the Context Help content.', 'rrze-univis') . '</p>',
            ],
            'title' => __('Overview', 'rrze-univis'),
            'sidebar' => sprintf('<p><strong>%1$s:</strong></p><p><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></p><p><a href="https://github.com/RRZE Webteam">%2$s</a></p>', __('For more information', 'rrze-univis'), __('RRZE Webteam on Github', 'rrze-univis')),
        ],
    ];
}

/**
 * Gibt die Einstellungen der Optionsbereiche zurück.
 * @return array [description]
 */
function getSections()
{
    return [
        [
            'id' => 'basic',
            'title' => __('UnivIS Settings', 'rrze-univis'),
        ],
    ];
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
function getFields()
{
    return [
        'basic' => [
            [
                'name' => 'univis_url',
                'label' => __('Link to <b><i>Univ</i>IS</b>', 'rrze-univis'),
                'desc' => __('', 'rrze-univis'),
                'placeholder' => __('', 'rrze-univis'),
                'type' => 'text',
                'default' => 'https://univis.uni-erlangen.de',
                'sanitize_callback' => 'sanitize_url',
            ],
            [
                'name' => 'univis_linktxt',
                'label' => __('Text to <b><i>Univ</i>IS</b> link', 'rrze-univis'),
                'desc' => __('', 'rrze-univis'),
                'placeholder' => __('', 'rrze-univis'),
                'type' => 'text',
                'default' => __('<b><i>Univ</i>IS</b> - Information System of the FAU', 'rrze-univis'),
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => 'UnivISOrgNr',
                'label' => __('<b><i>Univ</i>IS</b> OrgNr.', 'rrze-univis'),
                'desc' => __('', 'rrze-univis'),
                'placeholder' => '',
                'type' => 'text',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => 'semesterMin',
                'label' => __('Find Lectures from the soúmmer semester', 'rrze-univis'), 
                'desc' => __('', 'rrze-univis'),
                'placeholder' => '',
                'min' => 0,
                'max' => 99999999999,
                'step' => '1',
                'type' => 'number',
                'default' => date("Y") - 1,
                'sanitize_callback' => 'floatval',
            ],
            [
                'name' => 'wsStart',
                'label' => __('Lectures begin this winter semester', 'rrze-univis'), 
                'desc' => __('', 'rrze-univis'),
                'placeholder' => '',
                'type' => 'date',
                'default' => date("Y") - 1 . '-11-02',
                'sanitize_callback' => 'date',
            ],
            [
                'name' => 'wsEnd',
                'label' => __('End of the lecture period this winter semester', 'rrze-univis'), 
                'desc' => __('', 'rrze-univis'),
                'placeholder' => '',
                'type' => 'date',
                'default' => date("Y") . '-02-12',
                'sanitize_callback' => 'date',
            ],
            [
                'name' => 'ssStart',
                'label' => __('Lectures begin this summer semester', 'rrze-univis'), 
                'desc' => __('', 'rrze-univis'),
                'placeholder' => '',
                'type' => 'date',
                'default' => date("Y") . '-04-12',
                'sanitize_callback' => 'date',
            ],
            [
                'name' => 'ssEnd',
                'label' => __('End of the lecture period this summer semester', 'rrze-univis'), 
                'desc' => __('', 'rrze-univis'),
                'placeholder' => '',
                'type' => 'date',
                'default' => date("Y") . '-07-16',
                'sanitize_callback' => 'date',
            ],
            [
                'name' => 'hstart',
                'label' => __('Size of heading where output starts', 'rrze-univis'), 
                'desc' => __('', 'rrze-univis'),
                'min' => 2,
                'max' => 10,
                'step' => '1',
                'type' => 'number',
                'default' => '2',
                'sanitize_callback' => 'floatval',
            ],
        ],
    ];
}

/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings()
{
    return [
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
                    [
                        'id' => 'mitarbeiter-einzeln',
                        'val' => __('Single employee', 'rrze-univis'), 
                    ],
                    [
                        'id' => 'mitarbeiter-alle',
                        'val' => __('All employees', 'rrze-univis'), 
                    ],
                    [
                        'id' => 'mitarbeiter-telefonbuch',
                        'val' => __('Employees phone book', 'rrze-univis'), 
                    ],
                    [
                        'id' => 'mitarbeiter-orga',
                        'val' => __('Employees organization', 'rrze-univis'), 
                    ],
                ],
                'default' => 'mitarbeiter-alle',
                'field_type' => 'select',
                'label' => __('Please select', 'rrze-univis'), 
                'type' => 'string',
            ],
            'name' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Last name, first name', 'rrze-univis'), 
                'type' => 'string',
            ],
            'univisid' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('UnivIS ID person', 'rrze-univis'),
                'type' => 'string',
            ],
            'show' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('show', 'rrze-univis'), 
                'type' => 'string',
            ],
            'hide' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('hide', 'rrze-univis'), 
                'type' => 'string',
            ],
            'ignoriere_jobs' => [
                'default' => 'Sicherheitsbeauftragter|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte|Security commissary|IT-security commissary|Local UnivIS administration',
                'field_type' => 'text',
                'label' => __('Ignore jobs - separate individual activities with | from each other.', 'rrze-univis'), 
                'type' => 'string',
            ],
            'zeige_jobs' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Show these jobs only', 'rrze-univis'), 
                'type' => 'string',
            ],
            'call' => [
                'field_type' => 'toggle',
                'label' => __('Make phone numbers dialable', 'rrze-univis'), 
                'type' => 'boolean',
                'default' => true,
                'checked' => true,
            ],
            'number' => [
                'field_type' => 'text',
                'label' => __('UnivIS OrgID', 'rrze-univis'),
                'default' => '',
                'type' => 'string',
            ],
            'hstart' => [
                'default' => 2,
                'field_type' => 'text',
                'label' => __('Size of heading where output starts', 'rrze-univis'),
                'type' => 'number',
            ],
            'show_phone' => [
                'field_type' => 'toggle',
                'label' => __('Show phone numbers', 'rrze-univis'),
                'type' => 'boolean',
                'default' => true,
                'checked' => true,
            ],
            'show_mail' => [
                'field_type' => 'toggle',
                'label' => __('Show eMail', 'rrze-univis'),
                'type' => 'boolean',
                'default' => true,
                'checked' => true,
            ],
            'show_jumpmarks' => [
                'field_type' => 'toggle',
                'label' => __('Show anchors', 'rrze-univis'),
                'type' => 'boolean',
                'default' => true,
                'checked' => true,
            ],
            'lang' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Language', 'rrze-univis'),
                'type' => 'string',
            ],
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
                    [
                        'id' => 'lehrveranstaltungen-einzeln',
                        'val' => __('Single lecture', 'rrze-univis'), 
                    ],
                    [
                        'id' => 'lehrveranstaltungen-alle',
                        'val' => __('All lectures', 'rrze-univis'), 
                    ],
                ],
                'default' => 'lehrveranstaltungen-alle',
                'field_type' => 'select',
                'label' => __('Please select', 'rrze-univis'), 
                'type' => 'string',
            ],
            'id' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('UnivIS ID lecture', 'rrze-univis'),
                'type' => 'string',
            ],
            'name' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Last name, first name', 'rrze-univis'),
                'type' => 'string',
            ],
            'univisid' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('UnivIS ID person', 'rrze-univis'),
                'type' => 'string',
            ],
            'dozentid' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('UnivIS ID person', 'rrze-univis'),
                'type' => 'string',
            ],
            'lv_import' => [
                'field_type' => 'toggle',
                'label' => __('Output imported courses', 'rrze-univis'), 
                'type' => 'boolean',
                'default' => false,
                'checked' => false,
            ],
            'type' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Type f.e. vorl (=lecture)', 'rrze-univis'), 
                'type' => 'string',
            ],
            'order' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Sort by type f.e. "vorl,ueb"', 'rrze-univis'), 
                'type' => 'string',
            ],
            'sem' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Semester f.e. 2020w', 'rrze-univis'),
                'type' => 'string',
            ],
            'sprache' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('language', 'rrze-univis'),
                'type' => 'string',
            ],
            'number' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('UnivIS OrgID', 'rrze-univis'),
                'type' => 'string',
            ],
            'show' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('show', 'rrze-univis'),
                'type' => 'string',
            ],
            'hide' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('hide', 'rrze-univis'),
                'type' => 'string',
            ],
            'ics' => [
                'field_type' => 'toggle',
                'label' => __('Offer ICS', 'rrze-univis'), 
                'type' => 'boolean',
                'default' => true,
                'checked' => true,
            ],
            'hstart' => [
                'default' => 2,
                'field_type' => 'text',
                'label' => __('Size of heading where output starts', 'fau-person'),
                'type' => 'number',
            ],
            'fruehstud' => [
                'field_type' => 'toggle',
                'label' => __('Show early studies only', 'rrze-univis'), 
                'type' => 'boolean',
                'default' => null,
                'checked' => false,
            ],
            'gast' => [
                'field_type' => 'toggle',
                'label' => __('Show suitable for visiting students only', 'rrze-univis'), 
                'type' => 'boolean',
                'default' => null,
                'checked' => false,
            ],
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
                    [
                        'id' => 'publikationen',
                        'val' => __('Publications', 'rrze-univis'), 
                    ],
                ],
                'default' => 'publikationen',
                'field_type' => 'select',
                'label' => __('Please select', 'rrze-univis'),
                'type' => 'string',
            ],
            'name' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Last name, first name', 'rrze-univis'),
                'type' => 'string',
            ],
            'univisid' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('UnivIS ID person', 'rrze-univis'),
                'type' => 'string',
            ],
            'since' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __('Show from the specified year of publication. F.e. 2017', 'rrze-univis'), 
                'type' => 'number',
            ],
            'number' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('UnivIS OrgID', 'rrze-univis'),
                'type' => 'string',
            ],
            'hstart' => [
                'default' => 2,
                'field_type' => 'text',
                'label' => __('Size of heading where output starts', 'fau-person'),
                'type' => 'number',
            ],
        ],
    ];
}
