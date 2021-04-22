<?php

namespace RRZE\UnivIS;

class ICS {
    const DT_FORMAT = 'Ymd\THis';

    protected $properties = array();
    private $available_properties = array(
        'description',
        'starttime',
        'endtime',
        'startdate',
        'enddate',
        'dtend',
        'dtstart',
        'freq', 
        'repeat', 
        'rrule',
        'location',
        'summary',
        'url',
        'categories',
    );

    public function __construct($props){
        $this->set($props);
    }

    public function set($key, $val = false){
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            if (in_array($key, $this->available_properties)) {
                $this->properties[strtoupper($key)] = $this->sanitize_val($val, $key);
            }
        }
    }

    public function to_string(){
        $rows = $this->build_props();
        return implode("\r\n", $rows);
    }

    private function build_props(){
        // Build ICS properties - add header

        // CATEGORIES:U.S. Presidents,Civil War People
        // LOCATION:Hodgenville\, Kentucky
        // GEO:37.5739497;-85.7399606
        // DESCRIPTION:Born February 12\, 1809\nSixteenth President (1861-1865)\n\n\n
        //  \nhttp://AmericanHistoryCalendar.com
        // URL:http://americanhistorycalendar.com/peoplecalendar/1,328-abraham-lincol
        //  n
        $ics_props = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//FAU//Webteam v1.0',
            'BEGIN:VEVENT',
            'CALSCALE:GREGORIAN',
            'TZID:Europe/Berlin',
        ];

        $this->properties['DTSTART'] = (!empty($this->properties['STARTDATE']) ? $this->properties['STARTDATE'] : $this->properties['DTSTART']);
        $this->properties['DTEND'] = (!empty($this->properties['ENDDATE']) ? $this->properties['ENDDATE'] : $this->properties['DTSTART']);

        if (empty($this->properties['STARTDATE']) && empty($this->properties['REPEAT'])){
            $this->properties['REPEAT'] = 'MO,TU,WE,TH,FR';
        }

        $repeatEnd = '';
        $start = '';
        if (!empty($this->properties['REPEAT'])) {
            if ($this->properties['DTSTART'] == $this->properties['DTEND']) {
                // repeat for 1 year
                $repeatEnd = date(self::DT_FORMAT, strtotime('1 month', strtotime($this->properties['DTSTART'])));
            }


            $this->properties['STARTTIME'] = (!empty($this->properties['STARTTIME']) ? $this->properties['STARTTIME'] : '00:00');
            $this->properties['ENDTIME'] = (!empty($this->properties['ENDTIME']) ? $this->properties['ENDTIME'] : '00:00');
            $tsStart = strtotime($this->properties['DTSTART']);
            $start = date('Ymd', $tsStart);
            $day = date('Ymd', $start);
            $allowedDays = explode(',', $this->properties['REPEAT']);
            if (!in_array($day, $allowedDays)){
                // move to next possible date
                $dic = [
                    'MO' => 'Monday',
                    'TU' => 'Tuesday',
                    'WE' => 'Wednesday',
                    'TH' => 'Thursday',
                    'FR' => 'Friday',
                    'SA' => 'Saturday',
                    'SU' => 'Sunday',
                ];
                foreach($dic as $short => $long){
                    $nextPossibleDay = strtotime('next ' . $long);
                    if (in_array($short, $allowedDays) && $nextPossibleDay > $tsStart){
                        $start = date('Ymd', $nextPossibleDay);
                    }
                }
            }
            $repeatEnd = ($repeatEnd ? $repeatEnd : $this->properties['DTEND']);
            $this->properties['RRULE'] = 'FREQ=' . (!empty($this->properties['FREQ']) ? $this->properties['FREQ'] : 'WEEKLY') . ';UNTIL=' . $repeatEnd . ';WKST=MO;BYDAY=' . $this->properties['REPEAT'];

        }
        $start = (!empty($start) ? $start : $this->properties['STARTDATE']);
        $this->properties['DTSTART'] = date(self::DT_FORMAT, strtotime(date('Ymd', strtotime($start)) . date('Hi', strtotime($this->properties['STARTTIME']))));
        $this->properties['DTEND'] = date(self::DT_FORMAT, strtotime(date('Ymd', strtotime($this->properties['DTEND'])) . date('Hi', strtotime($this->properties['ENDTIME']))));
        $this->properties['DTEND'] = ($repeatEnd > $this->properties['DTEND'] ? $repeatEnd : $this->properties['DTEND']);
    
        unset($this->properties['REPEAT']);
        unset($this->properties['FREQ']);
        unset($this->properties['STARTTIME']);
        unset($this->properties['ENDTIME']);
        unset($this->properties['STARTDATE']);
        unset($this->properties['ENDDATE']);

        $props = array();
        foreach ($this->properties as $k => $v) {
            $props[strtoupper($k . ($k === 'URL' ? ';VALUE=URI' : ''))] = $v;
        }

        $props['DTSTAMP'] = $this->format_timestamp('now');
        $props['UID'] = uniqid();

        foreach ($props as $k => $v) {
            if (in_array($k, ['DTSTART', 'DTEND'])){
                $ics_props[] = $k . ';TZID=Europe/Berlin:' . $v;
                
            }else{
                $ics_props[] = "$k:$v";
            }
        }

        $ics_props[] = 'END:VEVENT';
        $ics_props[] = 'END:VCALENDAR';

        // echo '<pre>';
        // var_dump($ics_props);
        // exit;

        return $ics_props;
    }

    private function sanitize_val($val, $key = false){
        switch ($key) {
            case 'dtend':
                case 'dtstart':
                case 'dtstamp':
                    case 'startdate':
                        case 'enddate':
                                $val = $this->format_timestamp($val);
                break;
            case 'repeat':
            case 'freq':
                // do not escape
                break;    
            default:
                $val = $this->escape_string($val);
        }

        return $val;
    }

    private function format_timestamp($timestamp){
        $dt = new \DateTime($timestamp);
        return $dt->format(self::DT_FORMAT);
    }

    private function escape_string($str){
        $aReplace = [
            ';' => '.\n\n',
            '. ' => '.\n',

        ];
        // $str = preg_replace('/([\,])/', '\\\$1', $str);
        return str_replace(array_keys($aReplace), array_values($aReplace), $str);
    }
}
