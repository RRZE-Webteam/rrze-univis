<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Lifecycle {
    public static function activate(): void {
        Endpoints::add();
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        self::deleteOptions();
        self::deleteTransients();
        flush_rewrite_rules();
    }

    private static function deleteOptions(): void {
        $config = new Config();

        delete_option($config->getOptionName());
        delete_option('_rrze_univis');
        delete_option('univis-updated');
    }

    private static function deleteTransients(): void {
        global $wpdb;

        if (empty($wpdb) || empty($wpdb->options)) {
            return;
        }

        $config = new Config();
        $prefix = (string)$config->get('constants.cache.transient_prefix', 'rrze_univis_cache_');
        $transientLike = $wpdb->esc_like('_transient_' . $prefix) . '%';
        $timeoutLike = $wpdb->esc_like('_transient_timeout_' . $prefix) . '%';

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                $transientLike,
                $timeoutLike
            )
        );
    }
}
