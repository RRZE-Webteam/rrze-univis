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
    protected $plugin;
    protected $widget;
    protected $settings;
    protected $config;
    protected $template;
    
    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $this->config = new Config();
        $this->template = new Template($this->config, $this->plugin->getPath('templates'));
        add_action('init', [Endpoints::class, 'add']);
        add_action('template_redirect', [$this, 'getSingleEntry']);
    }

    public function onLoaded(): void {
        $ajax = new Ajax($this->plugin);
        $ajax->onLoaded();

        $settings = new Settings($this->plugin);
        $settings->onLoaded();

        $this->settings = $settings;

        $shortcode = new Shortcode($this->plugin);
        $shortcode->onLoaded();

        if ($this->metaboxEnabled($settings)) {
            $metabox = new Metabox();
            $metabox->onLoaded();
        }

        if ($this->widgetsEnabled($settings)) {
            $this->widget = new Widgets($this->plugin);
            add_action('widgets_init', [$this, 'loadWidget']);
            add_theme_support('widgets-block-editor');
            apply_filters('gutenberg_use_widgets_block_editor', get_theme_support('widgets-block-editor'));
        }
    }

    public function loadWidget(): void {
        register_widget($this->widget);
    }

    private function widgetsEnabled(Settings $settings): bool {
        return !empty($settings->options['basic_enable_widgets']) && $settings->options['basic_enable_widgets'] === true;
    }

    private function metaboxEnabled(Settings $settings): bool {
        return !empty($settings->options['basic_enable_metabox']) && $settings->options['basic_enable_metabox'] === true;
    }

    public function getSingleEntry(): void {
        global $wp_query;

        if (isset($wp_query->query_vars['lv_id'])) {
            $lectureId = sanitize_text_field((string)$wp_query->query_vars['lv_id']);
            $data = do_shortcode('[univis task="lehrveranstaltungen-einzeln" lv_id="' . esc_attr($lectureId) . '" ]');
        } elseif (isset($wp_query->query_vars['univisid'])) {
            $sShortcodeParams = '';
            $aParts = explode('_', sanitize_text_field((string)$wp_query->query_vars['univisid']));
            if (!empty($aParts[1])) {
                parse_str($aParts[1], $aParams);
                $show = !empty($aParams['show']) ? sanitize_text_field((string)$aParams['show']) : '';
                $hide = !empty($aParams['hide']) ? sanitize_text_field((string)$aParams['hide']) : '';
                $sShortcodeParams = 'show="' . esc_attr($show) . '" hide="' . esc_attr($hide) . '"';
            }
            $data = do_shortcode('[univis task="mitarbeiter-einzeln" univisid="' . esc_attr($aParts[0]) . '" ' . $sShortcodeParams . ']');
        } else {
            return;
        }
        
        // TODO: Hier den Titel der Seite aus dem erzeugten Content noch setzen und so setzen, dass wp_title diesen nutzen kann:
        // global $post;
        // $post->post_title = $title;
        
        echo $this->template->render('single-univis', [
            'data' => $data,
        ], $this);
        exit;
    }

    public static function getThemeGroup(): string {
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
