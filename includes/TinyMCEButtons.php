<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class TinyMCEButtons {

    public function __construct() {
        add_action('admin_init', [$this, 'shortcodeButtons']);
    }

    public function shortcodeButtons() {
        if (current_user_can('edit_posts') &&  current_user_can('edit_pages')) {
            add_filter('mce_external_plugins', [$this, 'addButtons']);
        }
    }

    public function addButtons($pluginArray) {
        $pluginArray['rrzeunivisshortcodes'] = plugins_url('../js/tinymce-shortcodes.js', plugin_basename(__FILE__));
        return $pluginArray;
    }
}
