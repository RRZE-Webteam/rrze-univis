<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

use function RRZE\UnivIS\Config\getConstants;
use RRZE\UnivIS\Settings;
use RRZE\UnivIS\Shortcode;

/**
 * Hauptklasse (Main)
 */
class Main
{
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;
    protected $widget;

    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
        add_action('init', 'RRZE\UnivIS\add_endpoint');
        add_action('template_redirect', [$this, 'getSingleEntry']);
    }

    public function onLoaded()
    {
        // add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        add_action('add_meta_boxes', [$this, 'addMetaboxes']);

        $functions = new Functions($this->pluginFile);
        $functions->onLoaded();

        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

        $this->settings = $settings;

        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();

        // Widget
        $this->widget = new UnivISWidget($this->pluginFile, $settings);
        add_action('widgets_init', [$this, 'loadWidget']);
        add_theme_support('widgets-block-editor');
        apply_filters('gutenberg_use_widgets_block_editor', get_theme_support('widgets-block-editor'));
    }

    public function loadWidget()
    {
        register_widget($this->widget);
    }

    public function addMetaboxes()
    {
        $aPosttypes = ['post', 'page', 'faq', 'glossary', 'synonym'];
        foreach ($aPosttypes as $posttype) {
            add_meta_box('get_univis_ids', __('Suche nach UnivIS IDs'), [$this, 'fillMetabox'], $posttype, 'side', 'core');
        }
    }

    public function fillMetabox()
    {
        ?>
            <div class="tagsdiv" id="univis">
                <div class="jaxtag">
                    <form method="post">
                    <div class="ajaxtag hide-if-no-js">
                        <select name="dataType" id="dataType" class="univisSelect" required="required">
                            <option value="departmentByName"><?php echo __('Organization', 'rrze-univis'); ?></option>
                            <option value="personByName"><?php echo __('Person', 'rrze-univis'); ?></option>
                            <option value="lectureByName"><?php echo __('Lecture', 'rrze-univis'); ?></option>
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

    public function getSingleEntry()
    {
        global $wp_query;

        if (isset($wp_query->query_vars['lv_id'])) {
            $data = do_shortcode('[univis task="lehrveranstaltungen-einzeln" lv_id="' . $wp_query->query_vars['lv_id'] . '" ]');
        } elseif (isset($wp_query->query_vars['univisid'])) {
            $sShortcodeParams = '';
            $aParts = explode('_', $wp_query->query_vars['univisid']);
            if (!empty($aParts[1])) {
                parse_str($aParts[1], $aParams);
                $sShortcodeParams = 'show="' . $aParams['show'] . '" hide="' . $aParams['hide'] . '"';
            }
            $data = do_shortcode('[univis task="mitarbeiter-einzeln" univisid="' . $aParts[0] . '" ' . $sShortcodeParams . ']');
        } else {
            return;
        }

        include plugin_dir_path($this->pluginFile) . 'templates/single-univis.php';
        exit;
    }

    public static function getThemeGroup()
    {
        $constants = getConstants();
        $ret = '';
        $active_theme = wp_get_theme();
        $active_theme = $active_theme->get('Name');

        if (in_array($active_theme, $constants['fauthemes'])) {
            $ret = 'fauthemes';
        } elseif (in_array($active_theme, $constants['rrzethemes'])) {
            $ret = 'rrzethemes';
        }
        return $ret;
    }

}
