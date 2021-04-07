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
            'title' => __('Basic Settings', 'rrze-univis')
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
                'label'             => __('UnivISOrgNr', 'rrze-univis'),
                'desc'              => __('', 'rrze-univis'),
                'placeholder'       => '',
                'min'               => 0,
                'max'               => 99999999999,
                'step'              => '1',
                'type'              => 'number',
                'default'           => 'Title',
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
		'block' => [
            'blocktype' => 'rrze-univis/univis',
			'blockname' => 'univis',
			'title' => 'RRZE-UnivIS',
			'category' => 'widgets',
            'icon' => 'admin-users',
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
		'id' => [
			'default' => 0,
			'field_type' => 'text',
			'label' => __( 'UnivIS ID Lehrveranstaltung oder Person', 'rrze-univis' ),
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
			'label' => __( 'Zeige Jobs', 'rrze-univis' ),
			'type' => 'string'
		],
		'task' => [
			'values' => [
                [
                    'id' => '',
                    'val' =>  __( 'Bitte wählen Sie', 'rrze-univis' )
                ],
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
                [
                    'id' => 'lehrveranstaltungen-einzeln',
                    'val' =>  __( 'Lehrveranstaltungen Einzeln', 'rrze-univis' )
                ],
                [
                    'id' => 'lehrveranstaltungen-alle',
                    'val' =>  __( 'Lehrveranstaltungen Alle', 'rrze-univis' )
                ],
			],
			'default' => 'wert1', // vorausgewählter Wert: Achtung: string, kein array!
			'field_type' => 'select',
			'label' => __( 'Beschriftung', 'rrze-univis' ),
			'type' => 'string' // Variablentyp des auswählbaren Werts
		],
    ];
}

