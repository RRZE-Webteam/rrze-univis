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
			'label' => __( 'Nachname,Vorname', 'rrze-univis' ),
			'type' => 'string'
		],
		'univisid' => [
			'default' => 0,
			'field_type' => 'text',
			'label' => __( 'UnivIS ID Person', 'rrze-univis' ),
			'type' => 'number'
		],
		'lv_id' => [
			'default' => 0,
			'field_type' => 'text',
			'label' => __( 'UnivIS ID Lehrveranstaltung', 'rrze-univis' ),
			'type' => 'number'
		],
		// 'Beispiel-Textarea-String' => [
		// 	'default' => 'ein Beispiel-Wert',
		// 	'field_type' => 'textarea',
		// 	'label' => __( 'Beschriftung', 'rrze-univis' ),
		// 	'type' => 'string',
		// 	'rows' => 5 // Anzahl der Zeilen 
		// ],
		// 'Beispiel-Radiobutton' => [
		// 	'values' => [
		// 		'wert1' => __( 'Wert 1', 'rrze-univis' ), // wert1 mit Beschriftung
		// 		'wert2' => __( 'Wert 2', 'rrze-univis' )
		// 	],
		// 	'default' => 'DESC', // vorausgewählter Wert
		// 	'field_type' => 'radio',
		// 	'label' => __( 'Order', 'rrze-univis' ), // Beschriftung der Radiobutton-Gruppe
		// 	'type' => 'string' // Variablentyp des auswählbaren Werts
		// ],
		// 'Beispiel-Checkbox' => [
		// 	'field_type' => 'checkbox',
		// 	'label' => __( 'Beschriftung', 'rrze-univis' ),
		// 	'type' => 'boolean',
		// 	'default'   => true // Vorauswahl: Haken gesetzt
        // ],
        // 'Beispiel-Toggle' => [
        //     'field_type' => 'toggle',
        //     'label' => __( 'Beschriftung', 'rrze-univis' ),
        //     'type' => 'boolean',
        //     'default'   => true // Vorauswahl: ausgewählt
        // ],
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
        // 'Beispiel-Multi-Select' => [
		// 	'values' => [
        //         [
        //             'id' => 'wert1',
        //             'val' =>  __( 'Wert 1', 'rrze-univis' )
        //         ],
        //         [
        //             'id' => 'wert2',
        //             'val' =>  __( 'Wert 2', 'rrze-univis' )
        //         ],
        //         [
        //             'id' => 'wert3',
        //             'val' =>  __( 'Wert 3', 'rrze-univis' )
        //         ],
		// 	],
		// 	'default' => ['wert1','wert3'], // vorausgewählte(r) Wert(e): Achtung: array, kein string!
		// 	'field_type' => 'multi_select',
		// 	'label' => __( 'Beschrifung', 'rrze-univis' ),
		// 	'type' => 'array',
		// 	'items'   => [
		// 		'type' => 'string' // Variablentyp der auswählbaren Werte
		// 	]
        // ]
    ];
}

