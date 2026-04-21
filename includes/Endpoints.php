<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Endpoints {
    public static function add(): void {
        $config = new Config();
        $constants = $config->getConstants();

        add_rewrite_endpoint($constants['endpoints']['person'], EP_PERMALINK | EP_PAGES);
        add_rewrite_endpoint($constants['endpoints']['lecture'], EP_PERMALINK | EP_PAGES);
    }
}
