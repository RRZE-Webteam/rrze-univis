<?php

/**
 * Plugin Name:     RRZE UnivIS
 * Plugin URI:      https://github.com/RRZE-Webteam/rrze-univis
 * Description:     Einbindung von Daten aus UnivIS
 * Version:         3.8.1
 * Requires at least: 6.9.4
 * Requires PHP:      8.3
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v3
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-univis
 */

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

use RRZE\UnivIS\Lifecycle;
use RRZE\UnivIS\Main;
use RRZE\UnivIS\Plugin;

// Automatische Laden von Klassen.
// Autoloader (PSR-4)
spl_autoload_register(__NAMESPACE__ . '\autoload');

function autoload(string $class): void {
    $prefix = __NAMESPACE__;
    $base_dir = __DIR__ . '/includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
}

const RRZE_PHP_VERSION = '8.3';
const RRZE_WP_VERSION = '6.9.4';

// Load the plugin's text domain for localization.
add_action('init', __NAMESPACE__ . '\loadTextdomain');
// Registriert die Plugin-Funktion, die bei Aktivierung des Plugins ausgeführt werden soll.
register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');
// Registriert die Plugin-Funktion, die ausgeführt werden soll, wenn das Plugin deaktiviert wird.
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');
// Wird aufgerufen, sobald alle aktivierten Plugins geladen wurden.
add_action('plugins_loaded', __NAMESPACE__ . '\loaded');


/**
 * Überprüft die Systemvoraussetzungen.
 */
function loadTextdomain(): void {
    load_plugin_textdomain('rrze-univis', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

function systemRequirements(): string {
    $error = '';
    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(__('The server is running PHP version %1$s. The Plugin requires at least PHP version %2$s.', 'rrze-univis'), PHP_VERSION, RRZE_PHP_VERSION);
    } elseif (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(__('The server is running WordPress version %1$s. The Plugin requires at least WordPress version %2$s.', 'rrze-univis'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }
    return $error;
}

/**
 * Wird nach der Aktivierung des Plugins ausgeführt.
 */
function activation(): void {
    // Überprüft die minimal erforderliche PHP- u. WP-Version.
    // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
    if ($error = systemRequirements()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die($error);
    }

    Lifecycle::activate();
}

/**
 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
 */
function deactivation(): void {
    Lifecycle::deactivate();
}

/**
 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
 * und alle Plugins eingebunden wurden.
 */
function loaded(): void {

    // Überprüft die Systemvoraussetzungen.
    if ($error = systemRequirements()) {
        $GLOBALS['rrze_univis_system_requirement_error'] = $error;
        add_action('admin_init', __NAMESPACE__ . '\registerSystemRequirementNotice');
        // Das Plugin wird nicht mehr ausgeführt.
        return;
    }

    // Hauptklasse (Main) wird instanziiert.
    $plugin = new Plugin(__FILE__);
    $plugin->loaded();

    $main = new Main($plugin);
    $main->onLoaded();
}

function registerSystemRequirementNotice(): void {
    $pluginData = get_plugin_data(__FILE__);
    $GLOBALS['rrze_univis_system_requirement_plugin_name'] = $pluginData['Name'];
    $tag = is_plugin_active_for_network(plugin_basename(__FILE__)) ? 'network_admin_notices' : 'admin_notices';
    add_action($tag, __NAMESPACE__ . '\showSystemRequirementNotice');
}

function showSystemRequirementNotice(): void {
    $pluginName = (string)($GLOBALS['rrze_univis_system_requirement_plugin_name'] ?? '');
    $error = (string)($GLOBALS['rrze_univis_system_requirement_error'] ?? '');

    printf(
        '<div class="notice notice-error"><p>' . __('Plugins: %1$s: %2$s', 'rrze-univis') . '</p></div>',
        esc_html($pluginName),
        esc_html($error)
    );
}
