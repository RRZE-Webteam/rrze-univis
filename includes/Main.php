<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

/**
 * Hauptklasse (Main)
 */
class Main {
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;
    protected $widget;
    protected $settings;
    protected $config;
    
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        $this->config = new Config();
        add_action('init', 'RRZE\UnivIS\add_endpoint');
        add_action('template_redirect', [$this, 'getSingleEntry']);
    }

    public function onLoaded() {
        $ajax = new Ajax($this->pluginFile);
        $ajax->onLoaded();

        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

        $this->settings = $settings;

        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();

        $metabox = new Metabox();
        $metabox->onLoaded();

        // Widget
        $this->widget = new Widgets($this->pluginFile, $settings);
        add_action('widgets_init', [$this, 'loadWidget']);
        add_theme_support('widgets-block-editor');
        apply_filters('gutenberg_use_widgets_block_editor', get_theme_support('widgets-block-editor'));
    }

    public function loadWidget() {
        register_widget($this->widget);
    }

    public function getSingleEntry() {
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
        
        // TODO: Hier den Titel der Seite aus dem erzeugten Content noch setzen und so setzen, dass wp_title diesen nutzen kann:
        // global $post;
        // $post->post_title = $title;
        
        include plugin_dir_path($this->pluginFile) . 'templates/single-univis.php';
        exit;
    }

    public static function getThemeGroup() {
        $config = new Config();
        $constants = $config->getConstants();
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
