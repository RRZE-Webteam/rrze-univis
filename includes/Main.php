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

        $functions = new Functions($this->pluginFile);
        $functions->onLoaded();

        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

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
            <form method="post">
            <div class="tagsdiv" id="univis">
            	<div class="jaxtag">
            		<div class="ajaxtag hide-if-no-js">
                            <select name="dataType" id="dataType" class="cmb2_select" required="required">
                                <option value="departmentByName"><?php echo __('Org Nr.', 'rrze-univis'); ?></option>
                                <option value="personByName"><?php echo __('ID der Person', 'rrze-univis'); ?></option>
                                <option value="lectureByName"><?php echo __('ID der Lehrveranstaltung', 'rrze-univis'); ?></option>
                            </select>     
                    </div>
                    <div class="ajaxtag hide-if-no-js">
                            <input type="text" name="keyword" id="keyword" value="" class="">
                            <input type="button" class="button tagadd" id="searchUnivisID" value="Search">
            	    </div>
                    <div id="univis-search-result" class="ajaxtag hide-if-no-js"></div>
                    <!-- <div id="loading"><i class="fa fa-refresh fa-spin fa-4x"></i></div> -->
                </div>
            </div>
            </form>
        <?php


    }
}
