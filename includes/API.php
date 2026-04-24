<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class API {

    protected $api;
    protected $orgID;
    protected $atts;
    protected $univisParam;
    protected $showJobs;
    protected $hideJobs;
    protected $sem;
    protected $gast;

    public function __construct(string $api, mixed $orgID, ?array $atts) {
        $this->setAPI($api);
        $this->orgID = $orgID;
        $this->atts = $atts;
        $this->sem = (!empty($this->atts['sem']) && Utils::checkSemester($this->atts['sem']) ? $this->atts['sem'] : '');
        $this->showJobs = (!empty($this->atts['zeige_jobs']) ? explode('|', $this->atts['zeige_jobs']) : []);
        $this->hideJobs = (!empty($this->atts['ignoriere_jobs']) ? explode('|', $this->atts['ignoriere_jobs']) : []);
        $this->gast = (!empty($this->atts['gast']) ? __('Allowed for guest students', 'rrze-univis') : '');
    }

    private function setAPI(string $api): void {
        // make sure we use https://DOMAIN/prg?search= no matter what input was made
        $this->api = preg_replace('/^((http|https):\/\/)?([^?\/]*)([\/?]*)/i', 'https://$3/prg?show=json&search=', $api, 1);
    }

    private function isHtmlResponse(string $body, string $contentType): bool {
        if ($contentType !== '' && stripos($contentType, 'text/html') !== false) {
            return true;
        }

        $trimmed = ltrim($body);

        return stripos($trimmed, '<!doctype html') === 0 || stripos($trimmed, '<html') === 0;
    }

    private function isNoResultHtml(string $body): bool {
        $plain = wp_strip_all_tags($body);
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = strtolower(preg_replace('/\s+/', ' ', $plain));

        $needles = [
            'keine passenden datens',
            'keine daten gefunden',
            'keine treffer',
            'no matching record',
            'no matching records',
            'no data found',
            'no entries found',
            'nothing found',
        ];

        foreach ($needles as $needle) {
            if (strpos($plain, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getResponsePreview(string $body): string {
        $plain = wp_strip_all_tags($body);
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = preg_replace('/\s+/', ' ', trim($plain));

        if ($plain === '') {
            return '';
        }

        return mb_substr($plain, 0, 180);
    }

    public function getData(string $dataType, mixed $univisParam = null): mixed {
        if (!empty($univisParam)) {
            $this->univisParam = urlencode($univisParam);
        }
        $url = $this->getUrl($dataType) . $this->univisParam;

        if (!$url) {
            return 'Set UnivIS Org ID in settings.';
        }

        $response = wp_remote_get($url, [
            'timeout' => 15,
            'redirection' => 3,
        ]);

        if (is_wp_error($response)) {
            do_action('rrze.log.error', 'UnivIS\\API (getData): request failed using ' . $url, $response);
            return false;
        }

        $statusCode = (int)wp_remote_retrieve_response_code($response);
        if ($statusCode < 200 || $statusCode >= 300) {
            do_action('rrze.log.error', 'UnivIS\\API (getData): unexpected response status ' . $statusCode . ' using ' . $url);
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        if ($body === '') {
            do_action('rrze.log.error', 'UnivIS\\API (getData): no data returned using ' . $url);
            return false;
        }

        $contentType = (string)wp_remote_retrieve_header($response, 'content-type');
        $data = json_decode($body, true);
        if (!is_array($data)) {
            if ($this->isHtmlResponse($body, $contentType) && $this->isNoResultHtml($body)) {
                return [];
            }

            do_action(
                'rrze.log.error',
                'UnivIS\\API (getData): expected JSON but received ' . ($contentType !== '' ? $contentType : 'unknown content type') . ' using ' . $url,
                [
                    'preview' => $this->getResponsePreview($body),
                ]
            );
            return false;
        }

        $transformer = new DataTransformer(
            $this->atts,
            $this->univisParam,
            $this->showJobs,
            $this->hideJobs,
            $this->gast,
            [$this, 'getData']
        );
        return $transformer->transform($dataType, $data);
    }

    private function getUrl(string $dataType): string|false {
        $url = $this->api;
        switch ($dataType) {
            case 'personByID':
                $url .= 'persons&id=';
                break;
            case 'personByName':
                $url .= 'persons&fullname=';
                break;
            case 'personAll':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'departments&number=' . $this->orgID;
                break;
            case 'personByOrga':
            case 'personByOrgaPhonebook':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'persons&department=' . $this->orgID;
                break;
            case 'publicationByAuthorID':
                $url .= 'publications&authorid=';
                break;
            case 'publicationByAuthor':
                $url .= 'publications&author=';
                break;
            case 'publicationByDepartment':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'publications&department=' . $this->orgID;
                break;
            case 'lectureByID':
                // $url .= 'lectures'.(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&id=';
                $url .= 'lectures' . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&id=';
                break;
            case 'lectureByDepartment':
                if (empty($this->orgID)) {
                    return false;
                }
                // $url .= 'lectures'.(!empty($this->atts['fruehstud'])?'&fruehstud='.($this->atts['fruehstud']?'ja':'nein'):'').(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&department='.$this->orgID;
                $url .= 'lectures' . (!empty($this->atts['fruehstud']) ? '&fruehstud=' . ($this->atts['fruehstud'] ? 'ja' : 'nein') : '') . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&department=' . $this->orgID;
                break;
            case 'lectureByLecturer':
                // $url .= 'lectures'.(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&lecturer=';
                $url .= 'lectures' . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&lecturer=';
                break;
            case 'lectureByLecturerID':
                // $url .= 'lectures'.(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&lecturerid=';
                $url .= 'lectures' . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&lecturerid=';
                break;
            case 'lectureByName':
                $url .= 'lectures&name=';
                break;
            case 'jobByID':
                $url .= 'positions&closed=1&id=';
                break;
            case 'jobAll':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'positions&closed=1&department=' . $this->orgID;
                break;
            case 'roomByID':
                $url .= 'rooms&id=';
                break;
            case 'roomByName':
                $url .= 'rooms&name=';
                break;
            case 'departmentByName':
                $url .= 'departments&name=';
                break;
            case 'departmentAll':
                $url .= 'departments';
                break;
            default:
                do_action('rrze.log.error', 'UnivIS\\API (getUrl): unknown dataType ' . $dataType);
                return false;
        }
        return $url;
    }

}
