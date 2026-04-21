<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class DataTransformer {
    protected $config;
    protected $atts;
    protected $univisParam;
    protected $showJobs;
    protected $hideJobs;
    protected $gast;
    protected $getDataCallback;

    public function __construct(?array $atts, mixed $univisParam, array $showJobs, array $hideJobs, string $gast, callable $getDataCallback) {
        $this->config = new Config();
        $this->atts = $atts;
        $this->univisParam = $univisParam;
        $this->showJobs = $showJobs;
        $this->hideJobs = $hideJobs;
        $this->gast = $gast;
        $this->getDataCallback = $getDataCallback;
    }

    public function transform(string $dataType, mixed &$data): mixed {
        $data = $this->mapIt($dataType, $data);
        $data = $this->dict($data);
        return $this->sortGroup($dataType, $data);
    }

    private function getData(string $dataType, mixed $univisParam = null): mixed {
        return call_user_func($this->getDataCallback, $dataType, $univisParam);
    }

    private function getMap(string $dataType): array {
        $map = [];

        switch ($dataType) {
            case 'personByID':
            case 'personByOrga':
            case 'personByOrgaPhonebook':
            case 'personByName':
            case 'personAll':
                $map = [
                    'node' => 'Person',
                    'fields' => [
                        'person_id' => 'id',
                        'key' => 'key',
                        'pub_visible' => 'pub_visible',
                        'title' => 'title',
                        'atitle' => 'atitle',
                        'firstname' => 'firstname',
                        'lastname' => 'lastname',
                        'work' => 'work',
                        'officehours' => 'officehour',
                        'department' => 'orgname',
                        'organization' => ['orgunit', 1],
                        'locations' => 'location',
                    ],
                ];
                break;
            case 'publicationByAuthor':
            case 'publicationByAuthorID':
            case 'publicationByDepartment':
                $map = [
                    'node' => 'Pub',
                    'fields' => [
                        'publication_id' => 'id',
                        'journal' => 'journal',
                        'pubtitle' => 'pubtitle',
                        'year' => 'year',
                        'author' => 'author',
                        'publication_type' => 'type',
                        'hstype' => 'hstype',
                    ],
                ];
                break;
            case 'lectureByID':
            case 'lectureByDepartment':
            case 'lectureByLecturer':
            case 'lectureByLecturerID':
            case 'lectureByName':
                $map = [
                    'node' => 'Lecture',
                    'fields' => [
                        'lecture_id' => 'id',
                        'name' => 'name',
                        'ects_name' => 'ects_name',
                        'comment' => 'comment',
                        'leclanguage' => 'leclanguage',
                        'key' => 'key',
                        'courses' => 'term',
                        'course_keys' => 'course',
                        'lecture_type' => 'type',
                        'keywords' => 'keywords',
                        'maxturnout' => 'maxturnout',
                        'url_description' => 'url_description',
                        'organizational' => 'organizational',
                        'summary' => 'summary',
                        'schein' => 'schein',
                        'sws' => 'sws',
                        'ects' => 'ects',
                        'ects_cred' => 'ects_cred',
                        'beginners' => 'beginners',
                        'fruehstud' => 'fruehstud',
                        'gast' => 'scientia',
                        'evaluation' => 'evaluation',
                        'doz' => 'doz',
                    ],
                ];
                break;
            case 'courses':
                $map = [
                    'node' => 'Lecture',
                    'fields' => [
                        'term' => 'term',
                        'coursename' => 'coursename',
                        'course_key' => 'key',
                        'doz' => 'doz',
                    ],
                ];
                break;
            case 'jobByID':
            case 'jobAll':
                $map = [
                    'node' => 'Position',
                    'fields' => [
                        'job_id' => 'id',
                        'application_end' => 'enddate',
                        'application_link' => 'desc6',
                        'job_intern' => 'intern',
                        'job_title' => 'title',
                        'job_start' => 'start',
                        'job_limitation' => 'type1',
                        'job_limitation_duration' => 'befristet',
                        'job_limitation_reason' => 'type3',
                        'job_salary_from' => 'vonbesold',
                        'job_salary_to' => 'bisbesold',
                        'job_qualifications' => 'desc2',
                        'job_qualifications_nth' => 'desc3',
                        'job_employmenttype' => 'type2',
                        'job_workhours' => 'wstunden',
                        'job_category' => 'group',
                        'job_description' => 'desc1',
                        'job_description_introduction' => 'desc5',
                        'job_experience' => 'desc2',
                        'job_benefits' => 'desc4',
                        'person_key' => 'acontact',
                    ],
                ];
                break;
            case 'roomByID':
            case 'roomByName':
                $map = [
                    'node' => 'Room',
                    'fields' => [
                        'room_id' => 'id',
                        'key' => 'key',
                        'name' => 'name',
                        'short' => 'short',
                        'roomno' => 'roomno',
                        'buildno' => 'buildno',
                        'north' => 'north',
                        'east' => 'east',
                        'address' => 'address',
                        'size' => 'size',
                        'description' => 'description',
                        'blackboard' => 'tafel',
                        'flipchart' => 'flip',
                        'beamer' => 'beam',
                        'microphone' => 'mic',
                        'audio' => 'audio',
                        'overheadprojector' => 'ohead',
                        'tv' => 'tv',
                        'internet' => 'inet',
                    ],
                ];
                break;
            case 'orga':
                $map = [
                    'node' => 'Org',
                    'fields' => [
                        'orga_positions' => 'job',
                    ],
                ];
                break;
            case 'departmentByName':
            case 'departmentAll':
                $map = [
                    'node' => 'Org',
                    'fields' => [
                        'orgnr' => 'orgnr',
                        'name' => 'name',
                    ],
                ];
                break;
        }

        return $map;
    }

    private function showPosition(string $position): bool {
        // show is given => show matches only
        if (!empty($this->showJobs) && !in_array($position, $this->showJobs)) {
            return false;
        }
        // hide defined jobs, show all others => config: ignoriere_jobs && shortcode: ignoriere_jobs
        if (!empty($this->hideJobs) && in_array($position, $this->hideJobs)) {
            return false;
        }
        return true;
    }

    private function mapIt(string $dataType, mixed &$data): mixed {
        $map = $this->getMap($dataType);

        if (empty($map)) {
            return $data;
        }

        $ret = [];
        $show = true;

        if (isset($data[$map['node']])) {
            foreach ($data[$map['node']] as $nr => $entry) {
                foreach ($map['fields'] as $k => $v) {
                    if (is_array($v)) {
                        if (is_int($v[1])) {
                            if (isset($data[$map['node']][$nr][$v[0]][$v[1]])) {
                                $ret[$nr][$k] = $data[$map['node']][$nr][$v[0]][$v[1]];
                            }
                            elseif (isset($data[$map['node']][$nr][$v[0]][0])) {
                                $ret[$nr][$k] = $data[$map['node']][$nr][$v[0]][0];
                            }
                        }
                        else {
                            $y = 0;
                            while (isset($data[$map['node']][$nr][$v[0]][$y][$v[1]])) {
                                $ret[$nr][$k] = $data[$map['node']][$nr][$v[0]][$y][$v[1]];
                                $y++;
                            }
                        }
                    }
                    else {
                        if (isset($data[$map['node']][$nr][$v])) {
                            $ret[$nr][$k] = $data[$map['node']][$nr][$v];
                        }
                    }
                }
            }
        }

        switch ($dataType) {
            case 'jobByID':
            case 'jobAll':
                // add person details
                $persons = $this->mapIt('personByID', $data);
                foreach ($ret as $e_nr => $entry) {
                    foreach ($persons as $person) {
                        if (isset($entry['person_key']) && $entry['person_key'] == $person['key']) {
                            unset($person['person_id']);
                            $ret[$e_nr] = array_merge_recursive($entry, $person);
                            unset($ret[$e_nr]['person_key']);
                            unset($ret[$e_nr]['key']);
                        }
                    }
                }
                break;
            case 'publicationByAuthorID':
            case 'publicationByAuthor':
            case 'publicationByDepartment':
                // add person details
                $persons = $this->mapIt('personByID', $data);
                foreach ($ret as $e_nr => $entry) {
                    foreach ($entry['author'] as $details) {
                        foreach ($persons as $p_nr => $person) {
                            if ($person['key'] == $details['pkey']) {
                                unset($person['key']);
                                $ret[$e_nr]['authors'][] = $person;
                                unset($person[$p_nr]);
                            }
                        }
                    }
                    unset($ret[$e_nr]['author']);
                }
                break;
            case 'lectureByLecturerID':
                // $lecturer_key is used in template to filter courses that are not by this lecturer
                $lecturer = $this->getData('personByID', $this->univisParam);
                if (isset($lecturer[0]['key'])) {
                    $subs = explode('Person.', $lecturer[0]['key']);
                }
                $lecturer_key = (isset($subs[1]) ? $subs[1] : '');
            case 'lectureByLecturer':
                // $lecturer_key is used in template to filter courses that are not by this lecturer
                $lecturer = $this->getData('personByName', $this->univisParam);
                if (isset($lecturer[0]['key'])) {
                    $subs = explode('Person.', $lecturer[0]['key']);
                }
                $lecturer_key = (isset($subs[1]) ? $subs[1] : '');
            case 'lectureByID':
            case 'lectureByDepartment':
                // add details
                $courses = $this->mapIt('courses', $data);
                $persons = $this->mapIt('personByID', $data);
                $delNr = [];
                foreach ($ret as $e_nr => $entry) {
                    $ret[$e_nr]['lecturer_key'] = (!empty($lecturer_key) ? $lecturer_key : '');
                    // add course details
                    if (isset($entry['course_keys'])) {
                        foreach ($entry['course_keys'] as $course_key) {
                            foreach ($courses as $c_nr => $course) {
                                if (($course['course_key'] == 'Lecture.' . $course_key) && (isset($course['term']))) {
                                    unset($course['course_key']);
                                    $ret[$e_nr]['courses'][] = $course;
                                    // delete entry of this course
                                    foreach ($ret as $nr => $val) {
                                        if ($val['key'] == 'Lecture.' . $course_key) {
                                            $delNr[] = $nr;
                                        }
                                    }
                                }
                            }
                        }
                        unset($ret[$e_nr]['course_keys']);
                    }
                    elseif (isset($entry['courses'])) {
                        unset($ret[$e_nr]['courses']);
                        $ret[$e_nr]['courses'][] = ['term' => $entry['courses']];
                    }
                    // add person details
                    if (isset($entry['doz'])) {
                        foreach ($entry['doz'] as $doz_key) {
                            foreach ($persons as $p_nr => $person) {
                                if ($person['key'] == 'Person.' . $doz_key) {
                                    // unset($person['key']);
                                    $ret[$e_nr]['lecturers'][] = $person;
                                    unset($person[$p_nr]);
                                }
                            }
                        }
                        unset($ret[$e_nr]['doz']);
                    }
                }
                foreach ($delNr as $nr) {
                    unset($ret[$nr]);
                }
                // add room details
                $rooms = $this->mapIt('roomByID', $data);
                foreach ($ret as $nr => $entry) {
                    if (isset($entry['courses'])) {
                        foreach ($entry['courses'] as $c_nr => $course) {
                            foreach ($course['term'] as $t_nr => $term) {
                                foreach ($rooms as $room) {
                                    if (isset($term['room']) && $term['room'] == $room['key']) {
                                        $ret[$nr]['courses'][$c_nr]['term'][$t_nr]['room'] = $room;
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case 'personAll':
                // add orga details
                $orga = $this->mapIt('orga', $data);
                $persons = [];
                foreach ($ret as $entry) {
                    $persons[$entry['key']] = $entry;
                }

                if (!empty($orga[0]['orga_positions'])) {
                    $orgaPositions = $orga[0]['orga_positions'];
                    foreach ($orgaPositions as $orgaDetails) {
                        if (isset($orgaDetails['per'])) {
                            foreach ($orgaDetails['per'] as $personKey) {
                                if (!empty($persons['Person.' . $personKey])) {
                                    if (!empty($persons['Person.' . $personKey]['orga_position'])) {
                                        $persons[] = $persons['Person.' . $personKey];
                                    }
                                    if (!empty($this->atts['lang'])) {
                                        $this->atts['lang'] = strtolower($this->atts['lang']);
                                        $desc = !empty($orgaDetails['description_' . $this->atts['lang']]) ? $orgaDetails['description_' . $this->atts['lang']] : __('Other', 'rrze-univis');
                                    }
                                    else {
                                        $desc = $orgaDetails['description'];
                                    }
                                    $persons['Person.' . $personKey]['orga_position'] = $desc;
                                    $persons['Person.' . $personKey]['orga_position_order'] = $orgaDetails['joborder'];
                                }
                            }
                        }
                    }
                }
                $ret = $persons;
                break;
        }

        return $ret;
    }

    private function sortGroup(string $dataType, mixed &$data): mixed {
        if (empty($data)) {
            return [];
        }
        // sort
        // 2024-01-10 (lapmk) fix mitarbeiter-telefonbuch: missing sorting by lastname
        if (in_array($dataType, ['personByID', 'personByOrga', 'personByName', 'personByOrgaPhonebook', ''])) {
            usort($data, [$this, 'sortByLastname']);
        }

        // group by department
        if ($dataType == 'personByOrga') {
            $data = $this->groupBy($data, 'department');
        }

        // group by lastname's first letter
        if ($dataType == 'personByOrgaPhonebook') {
            foreach ($data as $nr => $entry) {
                $data[$nr]['letter'] = mb_substr($entry['lastname'], 0, 1);
            }
            $data = $this->groupBy($data, 'letter');
        }
        // group by lecture_type_long
        if (in_array($dataType, ['lectureByID', 'lectureByLecturerID', 'lectureByLecturer', 'lectureByDepartment'])) {

            // 2021-09-23 quickfix because there is a bug in UnivIS-API's filtering by language
            if (!empty($this->atts['lang'])) {
                $data = $this->filterByLang($data);
            }

            // 2021-10-01 quickfix because there is a bug in UnivIS-API's filtering by type
            if (!empty($this->atts['type'])) {
                $data = $this->filterByType($data);
            }

            // 2022-01-13 UnivIS-API's does not support filtering by gast ("für Gaststudium geeignet")
            if (!empty($this->atts['gast'])) {
                $data = $this->filterByGast($data);
            }

            $data = $this->groupBy($data, 'lecture_type_long');

            // sort by attribute "order"
            if (!empty($this->atts['order'])) {
                $aOrder = explode(',', $this->atts['order']);
                $sortedData = [];
                foreach ($aOrder as $order) {
                    foreach ($data as $lecture_type_long => $lectures) {
                        foreach ($lectures as $lecture) {
                            if ($lecture['lecture_type'] == trim($order)) {
                                $sortedData[$lecture_type_long] = $data[$lecture_type_long];
                                unset($data[$lecture_type_long]);
                                break 1;
                            }
                        }
                    }
                }
                $data = $sortedData;
            }
        }
        // sort desc and group by year
        if (in_array($dataType, ['publicationByAuthorID', 'publicationByAuthor', 'publicationByDepartment'])) {
            usort($data, [$this, 'sortByYear']);

            // filter by attribute "since"
            if (!empty($this->atts['since'])) {
                $since = (int)$this->atts['since'];
                foreach ($data as $key => $entry) {
                    if ($entry["year"] < $since) {
                        unset($data[$key]);
                    }
                }
            }

            $data = $this->groupBy($data, 'year');
        }
        // sort orga_position_order and group by orga_position
        if ($dataType == 'personAll') {
            usort($data, [$this, 'sortByPositionorder']);
            $data = $this->groupBy($data, 'orga_position');
            foreach ($data as $position => $members) {
                $show = $this->showPosition($position);
                if (!$show) {
                    unset($data[$position]);
                }
                else {
                    usort($members, [$this, 'sortByLastname']);
                    $data[$position] = $members;
                }
            }
        }
        // sort by name
        if (in_array($dataType, ['departmentByName', 'departmentAll'])) {
            usort($data, [$this, 'sortByName']);
        }

        return $data;
    }

    private function filterVisibility(array $arr): array {
        $ret = [];

        $isAllowed = Utils::isInternAllowed();

        foreach ($arr as $key => $val) {
            if (!empty($val['pub_visible'])) {
                if (($val['pub_visible'] == 'ja')) {
                    $ret[$key] = $val;
                } elseif ($isAllowed) {
                    $ret[$key] = $val;
                }
            } else {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    private function filterByGast(array $arr): array {
        $ret = [];
        foreach ($arr as $key => $val) {
            if (!empty($val['gast']) && ($val['gast'] == $this->gast)) {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    private function filterByLang(array $arr): array {
        $ret = [];
        foreach ($arr as $key => $val) {
            if (!empty($val['leclanguage']) && ($val['leclanguage'] == $this->atts['lang'])) {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    private function multiMap(string $val): string {
        return trim(strtolower($val));
    }

    private function filterByType(array $arr): array {
        $ret = [];
        $aTypes = array_map([$this, 'multiMap'], explode(',', $this->atts['type']));

        foreach ($arr as $key => $val) {
            if (!empty($val['lecture_type']) && in_array($val['lecture_type'], $aTypes)) {
                $ret[$key] = $val;
            }
        }

        return $ret;
    }

    private function groupBy(array $arr, string $key): array {
        $ret = [];
        foreach ($arr as $val) {
            if (!empty($val[$key])) {
                $ret[$val[$key]][] = $val;
            }
        }
        return $ret;
    }

    // 2024-01-10 (lapmk) function to replace German umlaute for sorting, i.e. ä->ae, ß->ss, ... (used in function sortByLastname)
    private static function replaceUmlauteForSort(string $a): string {
        return str_replace(array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß'), array('Ae', 'ae', 'Oe', 'oe', 'Ue', 'ue', 'ss'), $a);
    }

    private function sortByLastname(array $a, array $b): int {
        // 2024-01-10 (lapmk) quickfix sorting of German umlaute
        return strcasecmp(self::replaceUmlauteForSort($a["lastname"]), self::replaceUmlauteForSort($b["lastname"]));
    }

    private function sortByName(array $a, array $b): int {
        return strcasecmp($a["name"], $b["name"]);
    }

    private function sortByYear(array $a, array $b): int {
        return strcasecmp($b["year"], $a["year"]);
    }

    private function sortByPositionorder(array $a, array $b): int {
        if (empty($a["orga_position_order"]) || empty($b["orga_position_order"])) {
            return 0;
        }
        return strnatcmp($a["orga_position_order"], $b["orga_position_order"]);
    }

    private function dict(mixed &$data): mixed {
        $fields = $this->config->get('constants.dictionary_fields', []);

        foreach ($data as $nr => $row) {
            foreach ($fields as $field => $values) {
                if (isset($data[$nr][$field]) && ($field == 'locations')) {
                    foreach ($data[$nr]['locations'] as $l_nr => $location) {
                        if (!empty($location['tel'])) {
                            $data[$nr]['locations'][$l_nr]['tel'] = Utils::correctPhone($data[$nr]['locations'][$l_nr]['tel']);
                            $data[$nr]['locations'][$l_nr]['tel_call'] = '+' . Utils::getInt($data[$nr]['locations'][$l_nr]['tel']);
                        }
                        if (!empty($location['fax'])) {
                            $data[$nr]['locations'][$l_nr]['fax'] = Utils::correctPhone($data[$nr]['locations'][$l_nr]['fax']);
                        }
                        if (!empty($location['mobile'])) {
                            $data[$nr]['locations'][$l_nr]['mobile'] = Utils::correctPhone($data[$nr]['locations'][$l_nr]['mobile']);
                            $data[$nr]['locations'][$l_nr]['mobile_call'] = '+' . Utils::getInt($data[$nr]['locations'][$l_nr]['mobile']);
                        }
                    }
                }
                elseif ($field == 'repeat') {
                    if (isset($data[$nr]['courses'])) {
                        foreach ($data[$nr]['courses'] as $c_nr => $course) {
                            foreach ($course['term'] as $m_nr => $meeting) {
                                if (isset($data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat'])) {
                                    $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeatNr'] = $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat'];
                                    $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat'] = str_replace(array_keys($values), array_values($values), $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat']);
                                }
                            }
                        }
                    }
                    elseif (isset($data[$nr]['officehours'])) {
                        foreach ($data[$nr]['officehours'] as $c_nr => $entry) {
                            if (isset($data[$nr]['officehours'][$c_nr]['repeat'])) {
                                $data[$nr]['officehours'][$c_nr]['repeatNr'] = $data[$nr]['officehours'][$c_nr]['repeat'];
                                $data[$nr]['officehours'][$c_nr]['repeat'] = trim(str_replace(array_keys($values), array_values($values), $data[$nr]['officehours'][$c_nr]['repeat']));
                            }
                        }
                    }
                }
                elseif ($field == 'organizational') {
                    if (isset($data[$nr][$field])) {
                        $data[$nr][$field] = Utils::formatUnivIS($data[$nr][$field]);
                    }
                }
                elseif (isset($data[$nr][$field])) {
                    if (in_array($field, ['title'])) {
                        // multi replace
                        $data[$nr][$field . '_long'] = str_replace(array_keys($values), array_values($values), $data[$nr][$field]);
                    }
                    else {
                        if (!is_array($values)) {
                            if ($field == 'sws') {
                                $data[$nr][$field] .= $values;
                            }
                            elseif ($field == 'ects_cred') {
                                $data[$nr][$field] = $values . $data[$nr][$field];
                            }
                            else {
                                $data[$nr][$field] = $values;
                            }
                        }
                        else {
                            if (isset($row[$field]) && isset($values[$row[$field]])) {
                                $data[$nr][$field . '_long'] = $values[$row[$field]];
                                if ($field == 'lecture_type') {
                                    $data[$nr][$field . '_short'] = trim(substr($values[$row[$field]], 0, strpos($values[$row[$field]], '(')));
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

}
