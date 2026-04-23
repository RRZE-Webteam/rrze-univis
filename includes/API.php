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

        $data = wp_remote_retrieve_body($response);
        if ($data === '') {
            do_action('rrze.log.error', 'UnivIS\\API (getData): no data returned using ' . $url);
            return false;
        }

        $data = json_decode($data, true);
        if (!is_array($data)) {
            do_action('rrze.log.error', 'UnivIS\\API (getData): invalid JSON returned using ' . $url);
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
