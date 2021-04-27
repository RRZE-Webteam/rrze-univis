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

        add_action('init', 'RRZE\UnivIS\add_endpoint');
        add_action('template_redirect', [$this, 'endpoint_template_redirect']);
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
        // add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
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

    // public function enqueueAdminScripts() {
    //     wp_register_style('rrze-univis', plugins_url('css/rrze-univis.css', plugin_basename($this->pluginFile)));
    //     wp_enqueue_style( 'rrze-univis' );
    // }

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

    public function endpoint_template_redirect()
    {
        global $wp_query;

        if (isset($wp_query->query_vars['univisid'])) {
            $slug = $wp_query->query_vars['univisid'];
            $key = 'univisid';
            $task = 'mitarbeiter-einzeln';
        } elseif (isset($wp_query->query_vars['lv_id'])) {
            $slug = $wp_query->query_vars['lv_id'];
            $key = 'lv_id';
            $task = 'lehrveranstaltungen-einzeln';
        } else {
            return;
        }

        if (!empty($slug)) {
            $slug = $key . '=' . $slug;
            $slugs = explode('&', $slug);
            $atts = [];

            foreach ($slugs as $k => $v) {
                $arr = explode('=', $v);
                $atts[$arr[0]] = $arr[1];
            }

            $this->controller->init($task, $atts);
            $data = $this->controller->ladeHTML();
        } else {
            $data = null;
        }

        $template = $this->locate_template();

        $this->load_template($template, $data);
        exit;
    }

    protected function locate_template()
    {
        $current_theme = wp_get_theme();
        $default_template = plugin_dir_path($this->plugin_file) . 'RRZE/UnivIS/Templates/single-univis.php';
        $template = '';

        foreach ($this->allowed_stylesheets as $theme => $style) {
            if (in_array(strtolower($current_theme->stylesheet), array_map('strtolower', $style))) {
                $template = plugin_dir_path($this->plugin_file) . "RRZE/UnivIS/Templates/Themes/$theme/single-univis.php";
                break;
            }
        }

        return !empty($template) && file_exists($template) ? $template : $default_template;
    }

    protected function load_template($template, $data = array())
    {
        include $template;
    }

}
