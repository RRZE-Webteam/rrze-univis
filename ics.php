<?php

namespace RRZE\UnivIS;

include_once(dirname(__FILE__) . '/includes/ICS.php');

header('Content-Type: text/calendar; charset=utf-8');

// https://www.nickless.test.rrze.fau.de/wp-16/wp-content/plugins/rrze-univis/ics.php?summary=V-V6+Funktionelle+Anatomie+des+Bewegungsapparates&url=https%3A%2F%2Fwww.nickless.test.rrze.fau.de%2Fwp-16

$summary = filter_input(INPUT_GET, 'summary', FILTER_SANITIZE_STRING);
$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_STRING);
$start = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_STRING);
$end = filter_input(INPUT_GET, 'end', FILTER_SANITIZE_STRING);
$repeat = filter_input(INPUT_GET, 'repeat', FILTER_SANITIZE_STRING);
$location = filter_input(INPUT_GET, 'location', FILTER_SANITIZE_STRING);


$props = [
    'summary' => (!empty($summary) ? $summary : NULL),
    'dtstart' => $dtstart,
    'dtend' => $dtend,
    'location' => 'this is my home',
    'location' => (!empty($location) ? $location : NULL),
    'url' => get_site_url(),
    ];

if (!empty($props['summary'])){
    $ics = new ICS($props);
    echo $ics->to_string();
}
