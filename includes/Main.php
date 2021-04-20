<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

use RRZE\UnivIS\Settings;
use RRZE\UnivIS\Shortcode;
use RRZE\UnivIS\TinyMCEButtons;



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

        new TinyMCEButtons();
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        add_action('add_meta_boxes', [$this, 'addMetaboxes']);

        $functions = new Functions($this->pluginFile);
        $functions->onLoaded();

        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();

        // Widget
        // add_action( 'widgets_init', [$this, 'loadWidget'] );    
        add_theme_support( 'widgets-block-editor' );
        apply_filters('gutenberg_use_widgets_block_editor', get_theme_support( 'widgets-block-editor' ));
    }

    public function enqueueAdminScripts() {
        wp_register_style('rrze-univis', plugins_url('css/rrze-univis.css', plugin_basename($this->pluginFile)));
        wp_enqueue_style( 'rrze-univis' );
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
                    <form method="post">
                    <div class="ajaxtag hide-if-no-js">
                        <select name="dataType" id="dataType" class="univisSelect" required="required">
                            <option value="departmentByName"><?php echo __('Organisation', 'rrze-univis'); ?></option>
                            <option value="personByName"><?php echo __('Person', 'rrze-univis'); ?></option>
                            <option value="lectureByName"><?php echo __('Lehrveranstaltung', 'rrze-univis'); ?></option>
                        </select>     
                    </div>
                    <div class="ajaxtag hide-if-no-js">
                        <input type="text" name="keyword" id="keyword" value="">
                        <input type="button" class="button tagadd" id="searchUnivisID" value="Search">
            	    </div>
                    <div class="ajaxtag hide-if-no-js">
                        <div id="univis-search-result"></div>
                        <div id="loading" class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i></div>
            	    </div>
                    </form>
                </div>
            </div>
        <?php
    }
}
