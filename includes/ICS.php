<?php

namespace RRZE\UnivIS;

class ICS {
    const DT_FORMAT = 'Ymd\THis';
    const VTIMEZONE = 'Europe/Berlin';

    protected $props = array();
    private $availableProps = array(
        'summary',
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
        'description',
        'url',
        'ssstart',
        'ssend',
        'wsstart',
        'wsend',
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
            if (in_array($key, $this->availableProps)) {
                $this->props[strtoupper($key)] = $this->sanitizeVal($val, $key);
            }
        }
    }

    public function toString(){
        $rows = $this->buildProps();
        return implode("\r\n", $rows);
    }

    private function buildProps(){
        // ICS Header
        $icsProps = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//FAU//Webteam v1.0',
            'CALSCALE:GREGORIAN',
            'BEGIN:VTIMEZONE',
            'TZID:' . self::VTIMEZONE,
            'TZURL:http://tzurl.org/zoneinfo-outlook/Europe/Berlin',
            'X-LIC-LOCATION:Europe/Berlin',
            'BEGIN:DAYLIGHT',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'TZNAME:CEST',
            'DTSTART:19700329T020000',
            'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'TZNAME:CET',
            'DTSTART:19701025T030000',
            'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU',
            'END:STANDARD',
            'END:VTIMEZONE',
            'BEGIN:VEVENT',
            'DTSTAMP:' . $this->formatTimestamp(''),
            'UID:' . uniqid() . '@fau.de',
        ];

        $this->props['DESCRIPTION'] .= (!empty($this->props['DESCRIPTION']) ? '\n\n' : '') . 'Information: ' . $this->props['URL'];
        $this->props['DTSTART'] = (!empty($this->props['STARTDATE']) ? $this->props['STARTDATE'] : $this->props['DTSTART']);
        $this->props['DTEND'] = (!empty($this->props['ENDDATE']) ? $this->props['ENDDATE'] : $this->props['DTSTART']);
        $this->props['STARTTIME'] = (!empty($this->props['STARTTIME']) ? $this->props['STARTTIME'] : '00:00');
        $this->props['ENDTIME'] = (!empty($this->props['ENDTIME']) ? $this->props['ENDTIME'] : '00:00');

        if (empty($this->props['STARTDATE']) && empty($this->props['REPEAT'])){
            $this->props['REPEAT'] = 'MO,TU,WE,TH,FR';
            $this->props['FREQ'] = 'WEEKLY;INTERVAL=1';
        }

        $start = '';
        $bRule = FALSE;
        if (!empty($this->props['REPEAT'])) {
            $tsStart = strtotime($this->props['DTSTART']);
            $start = date('Ymd', $tsStart);
            $day = date('Ymd', $start);
            $allowedDays = explode(',', $this->props['REPEAT']);
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
                        break 1;
                    }
                }
            }

            if (empty($this->props['ENDDATE'])) {
                // find enddate: either in winters' or summers' semester or if between +1 month 
                if ($start >= $this->props['SSSTART']) {
                    $this->props['ENDDATE'] = $this->formatTimestamp($this->props['SSEND']);
                } elseif ($start <= $this->props['WSEND']) {
                    $this->props['ENDDATE'] = $this->formatTimestamp($this->props['WSEND']);
                } else {
                    $this->props['ENDDATE'] = $this->formatTimestamp('1 month');
                }
            }
            $bRule = TRUE;
        }

        $start = (!empty($start) ? $start : $this->props['STARTDATE']);
        $this->props['DTSTART'] = date(self::DT_FORMAT, strtotime(date('Ymd', strtotime($start)) . date('Hi', strtotime($this->props['STARTTIME']))));
        $this->props['DTEND'] = date(self::DT_FORMAT, strtotime(date('Ymd', strtotime($this->props['DTSTART'])) . date('Hi', strtotime($this->props['ENDTIME']))));
        $this->props['ENDDATE'] = date(self::DT_FORMAT, strtotime(date('Ymd', strtotime($this->props['ENDDATE'])) . date('Hi', strtotime($this->props['ENDTIME']))));

        if ($bRule){
            $this->props['RRULE'] = 'FREQ=' . $this->props['FREQ'] . ';UNTIL=' . $this->props['ENDDATE'] . ';WKST=MO;BYDAY=' . $this->props['REPEAT'];
        }

        // delete everything ICS does not understand
        unset($this->props['REPEAT']);
        unset($this->props['FREQ']);
        unset($this->props['STARTTIME']);
        unset($this->props['ENDTIME']);
        unset($this->props['STARTDATE']);
        unset($this->props['ENDDATE']);
        unset($this->props['URL']); // allthough URL is defined in https://www.kanzaki.com/docs/ical/url.html an error occurs using iCal, therefore it is added to DESCRIPTION
        unset($this->props['SSSTART']);
        unset($this->props['SSEND']);
        unset($this->props['WSSTART']);
        unset($this->props['WSEND']);

        $props = array();
        foreach ($this->props as $k => $v) {
            $props[strtoupper($k . ($k === 'URL' ? ';VALUE=URI' : ''))] = $v;
        }

        foreach ($props as $k => $v) {
            if (in_array($k, ['DTSTART', 'DTEND'])){
                $icsProps[] = $k . ';TZID=' . self::VTIMEZONE . ':' . $v;
            }else{
                $icsProps[] = "$k:$v";
            }
        }

        // ICS Footer
        $icsProps[] = 'END:VEVENT';
        $icsProps[] = 'END:VCALENDAR';

        return $icsProps;
    }

    private function sanitizeVal($val, $key = false){
        switch ($key) {
            case 'dtend':
            case 'dtstart':
            case 'startdate':
            case 'enddate':
                $val = $this->formatTimestamp($val); // hier fehlt wohl noch die Uhrzeit
                break;
            case 'repeat':
            case 'freq':
                // do not beautifyString
                break;    
            default:
                $val = $this->beautifyString($val);
        }

        return $val;
    }

    private function formatTimestamp($timestamp){
        $dt = new \DateTime($timestamp);
        return $dt->format(self::DT_FORMAT);
    }

    private function beautifyString($str){
        $aReplace = [
            ';' => '.\n\n',
            // '. ' => '.\n',

        ];
        // $str = preg_replace('/([\,])/', '\\\$1', $str);
        return str_replace(array_keys($aReplace), array_values($aReplace), $str);
    }
}