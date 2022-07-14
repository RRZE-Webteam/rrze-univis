<?php

namespace RRZE\UnivIS;

class ICS
{
    const DT_FORMAT = 'Ymd\THis';
    const VTIMEZONE = 'Europe/Berlin';

    protected $props = array();
    private $availableProps = array(
        'SUMMARY',
        'DTSTART',
        'DTEND',
        'UNTIL',
        'FREQ',
        'REPEAT',
        'RRULE',
        'LOCATION',
        'DESCRIPTION',
    );

    public function __construct($props)
    {
        $this->set($props);
    }

    public function set($key, $val = false)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            if (in_array($key, $this->availableProps)) {
                $this->props[$key] = $this->sanitizeVal($val, $key);
            }
        }
    }

    public function toString()
    {
        $rows = $this->buildProps();
        return implode("\r\n", $rows);
    }

    private function buildProps()
    {
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

        if (!empty($this->props['REPEAT'])){
            $this->props['RRULE'] = 'FREQ=' . $this->props['FREQ'] . ';UNTIL=' . $this->props['UNTIL'] . ';WKST=MO;BYDAY=' . $this->props['REPEAT'];
        }else{
            unset($this->props['RRULE']);
        }

        // delete everything ICS does not understand
        unset($this->props['FREQ']);
        unset($this->props['UNTIL']);
        unset($this->props['REPEAT']);

        $props = array();
        foreach ($this->props as $k => $v) {
            $props[strtoupper($k . ($k === 'URL' ? ';VALUE=URI' : ''))] = $v;
        }

        foreach ($props as $k => $v) {
            if (in_array($k, ['DTSTART', 'DTEND'])) {
                $icsProps[] = $k . ';TZID=' . self::VTIMEZONE . ':' . $v;
            } else {
                $icsProps[] = "$k:$v";
            }
        }

        // ICS Footer
        $icsProps[] = 'END:VEVENT';
        $icsProps[] = 'END:VCALENDAR';

        return $icsProps;
    }

    private function sanitizeVal($val, $key = false)
    {
        switch ($key) {
            case 'DTEND':
            case 'DTSTART':
                $val = $this->formatTimestamp($val); // hier fehlt wohl noch die Uhrzeit
                break;
            case 'REPEAT':
            case 'FREQ':
                // do not beautifyString
                break;
            default:
                $val = $this->beautifyString($val);
        }

        return $val;
    }

    private function formatTimestamp($timestamp)
    {
        $dt = new \DateTime($timestamp);
        return $dt->format(self::DT_FORMAT);
    }

    private function beautifyString($str)
    {
        $aReplace = [
            ';' => '.\n\n',
            // '. ' => '.\n',

        ];
        // $str = preg_replace('/([\,])/', '\\\$1', $str);
        return str_replace(array_keys($aReplace), array_values($aReplace), $str);
    }
}
