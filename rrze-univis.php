<?php

/**
 * Plugin Name:     RRZE UnivIS
 * Plugin URI:      https://github.com/RRZE-Webteam/rrze-univis
 * Description:     Einbindung von Daten aus UnivIS für den Geschäftsverteilungsplan.
 * Version:         2.2.2
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-univis
 */

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

const RRZE_PHP_VERSION = '5.5';
const RRZE_WP_VERSION = '4.9';

register_activation_hook(__FILE__, 'RRZE\UnivIS\activation');
register_deactivation_hook(__FILE__, 'RRZE\UnivIS\deactivation');

add_action('plugins_loaded', 'RRZE\UnivIS\loaded');

/*
 * Einbindung der Sprachdateien.
 * @return void
 */
function load_textdomain() {
    load_plugin_textdomain('rrze-univis', FALSE, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/*
* Wird durchgeführt, nachdem das Plugin aktiviert wurde.
* @return void
*/
function activation() {
    // Sprachdateien werden eingebunden.
    load_textdomain();

    // Überprüft die minimal erforderliche PHP- u. WP-Version.
    system_requirements();
    
    // Endpoint hinzufügen
    add_endpoint(TRUE);
    flush_rewrite_rules();
}

/*
 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
 * @return void
 */
function deactivation() {
    flush_rewrite_rules();
}

function add_endpoint() {
    add_rewrite_endpoint('univisid', EP_PERMALINK | EP_PAGES);
    add_rewrite_endpoint('lv_id', EP_PERMALINK | EP_PAGES);
}
 
 /*
  * Überprüft die minimal erforderliche PHP- u. WP-Version.
  * @return void
  */
function system_requirements() {
    $error = '';

    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(__('Your server is running PHP version %s. Please upgrade at least to PHP version %s.', 'rrze-univis'), PHP_VERSION, RRZE_PHP_VERSION);
    }

    if (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(__('Your Wordpress version is %s. Please upgrade at least to Wordpress version %s.', 'rrze-univis'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }

    // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
    if (!empty($error)) {
        deactivate_plugins(plugin_basename(__FILE__), FALSE, TRUE);
        wp_die($error);
    }
}

/*
 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
 * und alle Plugins eingebunden wurden.
 * @return void
 */
function loaded() {
    // Sprachdateien werden eingebunden.
    load_textdomain();
        
    // Automatische Laden von Klassen.
    autoload();
}

/*
 * Automatische Laden von Klassen.
 * @return void
 */
function autoload() {
    require 'autoload.php';    
    $main = new Main(__FILE__);
}
