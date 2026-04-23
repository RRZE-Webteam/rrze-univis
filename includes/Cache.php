<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Cache {
    private $api;
    private $config;
    private $atts;
    private $orgID;
    private $noCache;
    private $transientPrefix;
    private $transientJitterMinutes;
    private $transientTimes;
    private $dataTypes;

    public function __construct(string $api, mixed $orgID, ?array $atts = null, bool $noCache = false) {
        $this->config = new Config();
        $cacheConfig = $this->config->get('constants.cache', []);

        $this->api = new API($api, $orgID, $atts);
        $this->atts = $atts;
        $this->orgID = $orgID;
        $this->noCache = $noCache;
        $this->transientPrefix = $cacheConfig['transient_prefix'] ?? 'rrze_univis_cache_';
        $this->transientJitterMinutes = (int)($cacheConfig['transient_jitter_minutes'] ?? 0);
        $this->transientTimes = $cacheConfig['transient_times'] ?? ['default' => 1440];
        $this->dataTypes = $cacheConfig['data_types'] ?? [];
    }

    public function getData(string $dataType, mixed $univisParam = null): mixed {
        $dataType = $this->sanitizeDataType($dataType);
        if ($dataType === null) {
            return false;
        }

        $key = $this->buildTransientKey($dataType, $univisParam);
        if (!$this->noCache) {
            $cached = get_transient($key);
            if ($cached && $cached != __('No matching records found.', 'rrze-univis')) {
                return $cached;
            }
        }

        $data = $this->api->getData($dataType, $univisParam);
        $this->set($key, $dataType, $data);
        return $data;
    }

    public function sanitizeDataType(string $dataType): ?string {
        $dataType = trim((string)$dataType);
        if (isset($this->dataTypes[$dataType])) {
            return $dataType;
        }

        do_action('rrze.log.error', 'UnivIS\\Cache (sanitizeDataType): unknown dataType ' . $dataType);
        return null;
    }

    public function getEndpointForDataType(string $dataType): string {
        return (string)($this->dataTypes[$dataType] ?? 'default');
    }

    public function buildTransientKey(string $dataType, mixed $univisParam = null): string {
        $endpoint = $this->getEndpointForDataType($dataType);
        $basis = [
            'endpoint' => $endpoint,
            'dataType' => $dataType,
            'atts' => $this->atts,
            'orgID' => $this->orgID,
            'param' => $univisParam,
        ];

        $json = function_exists('wp_json_encode') ? wp_json_encode($basis) : json_encode($basis);
        $key = $this->transientPrefix . $endpoint . '_' . md5((string)$json);

        if (strlen($key) > 172) {
            $key = $this->transientPrefix . md5($key);
        }

        return $key;
    }

    private function set(string $key, string $dataType, mixed $data): void {
        $endpoint = $this->getEndpointForDataType($dataType);
        $baseMinutes = (int)($this->transientTimes[$endpoint] ?? $this->transientTimes['default'] ?? 1440);
        $randomOffset = wp_rand(0, $this->transientJitterMinutes) * 60;
        $lifetime = ($baseMinutes * 60) + $randomOffset;

        set_transient($key, $data, $lifetime);
    }
}
