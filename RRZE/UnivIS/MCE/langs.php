<?php

namespace RRZE\UnivIS\MCE;

use _WP_Editors;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('_WP_Editors')) {
    require(ABSPATH . WPINC . '/class-wp-editor.php');
}

function mce_plugin_translation() {
    $strings = array(
        'add_univis' => __('Add UnivIS', 'rrze-univis'),
    );
    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.rrze_univis_mce_plugin", ' . json_encode($strings) . ")" . PHP_EOL;

    return $translated;
}

$strings = mce_plugin_translation();
