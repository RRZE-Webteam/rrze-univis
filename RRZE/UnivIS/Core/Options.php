<?php

namespace RRZE\UnivIS\Core;

defined('ABSPATH') || exit;

class Options
{
    protected $option_name = '_rrze_univis';

    public function __construct()
    {
    }

    /*
     * Standard Einstellungen werden definiert
     * @return array
     */
    public function default_options()
    {
        $options = [
            'univis_default_link' => __('<b><i>Univ</i>IS</b> - Information System of the FAU', 'rrze-univis'),
            'UnivISOrgNr' => '',
            'sortByLastname' => true
        ];

        return $options;
    }

    /*
     * Gibt die Einstellungen zurück.
     * @return object
     */
    public function get_options()
    {
        $defaults = self::default_options();

        $options = (array) get_option($this->option_name);
        $options = wp_parse_args($options, $defaults);
        $options = array_intersect_key($options, $defaults);

        return (object) $options;
    }

    public function get_option_name()
    {
        return $this->option_name;
    }
}
