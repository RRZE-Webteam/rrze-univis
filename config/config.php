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

/**
 * Gibt die Einstellungen des Menus zurück.
 * @return array [description]
 */
function getMenuSettings()
{
    return [
        'page_title'    => __('RRZE UnivIS', 'rrze-univis'),
        'menu_title'    => __('RRZE UnivIS', 'rrze-univis'),
        'capability'    => 'manage_options',
        'menu_slug'     => 'rrze-univis',
        'title'         => __('RRZE UnivIS Settings', 'rrze-univis'),
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
            'id'        => 'rrze-univis-help',
            'content'   => [
                '<p>' . __('Here comes the Context Help content.', 'rrze-univis') . '</p>'
            ],
            'title'     => __('Overview', 'rrze-univis'),
            'sidebar'   => sprintf('<p><strong>%1$s:</strong></p><p><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></p><p><a href="https://github.com/RRZE Webteam">%2$s</a></p>', __('For more information', 'rrze-univis'), __('RRZE Webteam on Github', 'rrze-univis'))
        ]
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
            'id'    => 'basic',
            'title' => __('UnivIS Settings', 'rrze-univis')
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
                'name'              => 'univis_url',
                'label'             => __('Link zu <b><i>Univ</i>IS</b>', 'rrze-univis'),
                'desc'              => __('', 'rrze-univis'),
                'placeholder'       => __('', 'rrze-univis'),
                'type'              => 'text',
                'default'           => 'https://univis.uni-erlangen.de',
                'sanitize_callback' => 'sanitize_url'
            ],
            [
                'name'              => 'univis_linktxt',
                'label'             => __('Text zum <b><i>Univ</i>IS</b> Link', 'rrze-univis'),
                'desc'              => __('', 'rrze-univis'),
                'placeholder'       => __('', 'rrze-univis'),
                'type'              => 'text',
                'default'           => __('<b><i>Univ</i>IS</b> - Information System of the FAU', 'rrze-univis'),
                'sanitize_callback' => 'sanitize_text_field'
            ],
            [
                'name'              => 'UnivISOrgNr',
                'label'             => __('<b><i>Univ</i>IS</b> OrgNr.', 'rrze-univis'),
                'desc'              => __('', 'rrze-univis'),
                'placeholder'       => '',
                'min'               => 0,
                'max'               => 99999999999,
                'step'              => '1',
                'type'              => 'number',
                'default'           => '',
                'sanitize_callback' => 'floatval'
            ],
            [
                'name'              => 'semesterMin',
                'label'             => __('Lehrveranstaltungen bis zum Sommersemester finden', 'rrze-univis'),
                'desc'              => __('', 'rrze-univis'),
                'placeholder'       => '',
                'min'               => 0,
                'max'               => 99999999999,
                'step'              => '1',
                'type'              => 'number',
                'default'           => '1971',
                'sanitize_callback' => 'floatval'
            ],            
        ],
    ];
}


/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings(){
	return [
        'mitarbeiter' => [
            'block' => [
                'blocktype' => 'rrze-univis/univismitarbeiter',
                'blockname' => 'univismitarbeiter',
                'title' => 'RRZE-UnivIS Mitarbeiter',
                'category' => 'widgets',
                'icon' => 'admin-users',
            ],
            'task' => [
                'values' => [
                    [
                        'id' => 'mitarbeiter-einzeln',
                        'val' =>  __( 'Mitarbeiter Einzeln', 'rrze-univis' )
                    ],
                    [
                        'id' => 'mitarbeiter-alle',
                        'val' =>  __( 'Mitarbeiter Alle', 'rrze-univis' )
                    ],
                    [
                        'id' => 'mitarbeiter-telefonbuch',
                        'val' =>  __( 'Mitarbeiter Telefonbuch', 'rrze-univis' )
                    ],
                    [
                        'id' => 'mitarbeiter-orga',
                        'val' =>  __( 'Mitarbeiter Organisation', 'rrze-univis' )
                    ],
                ],
                'default' => 'mitarbeiter-alle',
                'field_type' => 'select',
                'label' => __( 'Bitte wählen Sie', 'rrze-univis' ),
                'type' => 'string'
            ],
            'name' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Nachname, Vorname', 'rrze-univis' ),
                'type' => 'string'
            ],
            'univisid' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'UnivIS ID Person', 'rrze-univis' ),
                'type' => 'number'
            ],
            'show' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'anzeigen', 'rrze-univis' ),
                'type' => 'string'
            ],
            'hide' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'ausblenden', 'rrze-univis' ),
                'type' => 'string'
            ],
            'ignoriere_jobs' => [
                'default' => 'Sicherheitsbeauftragter|IT-Sicherheits-Beauftragter|Webmaster|Postmaster|IT-Betreuer|UnivIS-Beauftragte',
                'field_type' => 'text',
                'label' => __( 'Ignoriere Jobs - einzelne Tätigkeiten durch | voneinander trennen.', 'rrze-univis' ),
                'type' => 'string'
            ],
            'zeige_jobs' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Zeige nur diese Jobs', 'rrze-univis' ),
                'type' => 'string'
            ],
            'number' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'UnivIS OrgID', 'rrze-univis' ),
                'type' => 'number'
            ],
        ],
        'lehrveranstaltungen' => [
            'block' => [
                'blocktype' => 'rrze-univis/univislehrveranstaltungen',
                'blockname' => 'univislehrveranstaltungen',
                'title' => 'RRZE-UnivIS Lehrveranstaltungen',
                'category' => 'widgets',
                'icon' => 'bank',
            ],
            'task' => [
                'values' => [
                    [
                        'id' => 'lehrveranstaltungen-einzeln',
                        'val' =>  __( 'Lehrveranstaltungen Einzeln', 'rrze-univis' )
                    ],
                    [
                        'id' => 'lehrveranstaltungen-alle',
                        'val' =>  __( 'Lehrveranstaltungen Alle', 'rrze-univis' )
                    ],
                ],
                'default' => 'lehrveranstaltungen-alle',
                'field_type' => 'select',
                'label' => __( 'Bitte wählen Sie', 'rrze-univis' ),
                'type' => 'string'
            ],
            'id' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'UnivIS ID Lehrveranstaltung', 'rrze-univis' ),
                'type' => 'number'
            ],
            'name' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Nachname, Vorname', 'rrze-univis' ),
                'type' => 'string'
            ],
            'univisid' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'UnivIS ID Person', 'rrze-univis' ),
                'type' => 'number'
            ],
            'lv_import' => [
                'field_type' => 'toggle',
                'label' => __( 'Importierte Lehrveranstaltungen ausgeben', 'rrze-univis' ),
                'type' => 'boolean',
                'default' => TRUE,
                'checked'   => TRUE
            ],
            'type' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Typ. z.B. vorl (=Vorlesung)', 'rrze-univis' ),
                'type' => 'string'
            ],
            'order' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Sortierung nach Typ z.B. "vorl,ueb"', 'rrze-univis' ),
                'type' => 'string'
            ],
            'sem' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Semester z.B. 2020w', 'rrze-univis' ),
                'type' => 'string'
            ],
            'sprache' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Sprache', 'rrze-univis' ),
                'type' => 'string'
            ],
            'number' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'UnivIS OrgID', 'rrze-univis' ),
                'type' => 'number'
            ],
        ],
        'publikationen' => [
            'block' => [
                'blocktype' => 'rrze-univis/univispublikationen',
                'blockname' => 'univispublikationen',
                'title' => 'RRZE-UnivIS Publikationen',
                'category' => 'widgets',
                'icon' => 'megaphone',
            ],
            'task' => [
                'values' => [
                    [
                        'id' => 'publikationen',
                        'val' =>  __( 'Publikationen', 'rrze-univis' )
                    ],
                ],
                'default' => 'publikationen',
                'field_type' => 'select',
                'label' => __( 'Bitte wählen Sie', 'rrze-univis' ),
                'type' => 'string'
            ],
            'name' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __( 'Nachname, Vorname', 'rrze-univis' ),
                'type' => 'string'
            ],
            'univisid' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'UnivIS ID Person', 'rrze-univis' ),
                'type' => 'number'
            ],
            'since' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'Ab dem angegebenen Erscheinungsjahr anzeigen. Z.B. 2017', 'rrze-univis' ),
                'type' => 'number'
            ],
            'number' => [
                'default' => 0,
                'field_type' => 'text',
                'label' => __( 'UnivIS OrgID', 'rrze-univis' ),
                'type' => 'number'
            ],
        ]
    ];
}

