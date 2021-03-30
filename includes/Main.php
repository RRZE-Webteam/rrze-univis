<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

use RRZE\UnivIS\Settings;
use RRZE\UnivIS\Shortcode;


/**
 * Hauptklasse (Main)
 */
class Main {
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;

    /**
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

        // Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

        // Shortcode-Klasse wird instanziiert.
        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();
    }

    /**
     * Enqueue der globale Skripte.
     */
    public function enqueueScripts() {
        wp_register_style('rrze-univis', plugins_url('assets/css/plugin.css', plugin_basename($this->pluginFile)));
    }
}
