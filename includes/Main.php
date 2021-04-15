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
        add_action('add_meta_boxes', [$this, 'addMetaboxes']);


        // Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();


        // Shortcode-Klasse wird instanziiert.
        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();

        // Widget
        add_action( 'widgets_init', [$this, 'loadWidget'] );    
    }

    /**
     * Enqueue der globale Skripte.
     */
    public function enqueueScripts() {
        wp_register_style('rrze-univis', plugins_url('assets/css/plugin.css', plugin_basename($this->pluginFile)));
    }

    public function loadWidget() {
        $myWidget = new UnivISWidget();
        register_widget($myWidget);
    }

    public function addMetaboxes(){
        add_meta_box('get_univis_ids', __('Suche nach UnivIS IDs'), [$this, 'fillMetabox'], NULL, 'side', 'core');
    }

    public function fillMetabox() {
        ?>
            <div class="tagsdiv" id="univis">
            	<div class="jaxtag">
            		<div class="ajaxtag hide-if-no-js">
                        <form method="post">
                            <input type="hidden" name="action" value="search_univis">
                            <input type="text" name="unvis_keyword" id="keyword" value="" class="newtag form-input-tip ui-autocomplete-input">
                            <input type="button" class="button tagadd" value="Search">
                        </form>
            	    </div>
                </div>
            </div>
        <?php


    }
}
