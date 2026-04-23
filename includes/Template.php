<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Template {
    protected string $templateDir;
    protected Config $config;

    public function __construct(Config $config, string $templateDir) {
        $this->config = $config;
        $this->templateDir = rtrim($templateDir, '/') . '/';
    }

    public function render(string $templateName, array $templateData = [], ?object $context = null): string {
        $templatePath = $this->templateDir . $templateName . '.php';

        if (!file_exists($templatePath)) {
            do_action('rrze.log.error', 'UnivIS\\Template (render): Template file does not exist: ' . $templatePath);
            return '';
        }

        $renderer = function (string $templatePath, array $templateData): void {
            extract($templateData, EXTR_SKIP);
            include $templatePath;
        };

        if ($context !== null) {
            $renderer = $renderer->bindTo($context, get_class($context));
        }

        ob_start();
        $renderer($templatePath, $templateData);
        return (string)ob_get_clean();
    }
}
