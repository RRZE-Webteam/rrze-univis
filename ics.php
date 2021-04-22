<?php

namespace RRZE\UnivIS;

include_once(dirname(__FILE__) . '/includes/ICS.php');

header('Content-Type: text/calendar; charset=utf-8');

$input = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

$aFreq = [
    'woche' => 'WEEKLY',
    'monat' => 'MONTHLY',
    'jahr' => 'YEARLY',
    'zweite' => 'INTERVAL=2',
    'dritte' => 'INTERVAL=3',
];

$aDay = [
    'mo' => 'MO',
    'di' => 'TU',
    'mi' => 'WE',
    'do' => 'TH',
    'fr' => 'FR',
    'sa' => 'SA',
    'so' => 'SU',
];

if (!empty($input['repeat'])) {
    $input['freq'] = implode(';', array_intersect($aFreq, str_replace(array_keys($aFreq), array_values($aFreq), explode(' ', strtolower($input['repeat'])))));
    $input['repeat'] = implode(';', array_intersect($aDay, str_replace(array_keys($aDay), array_values($aDay), preg_split('/(\,| )/', strtolower($input['repeat'])))));
}

$input['dtstart'] = (empty($input['dtstart']) ? date() : $input['dtstart'] );
$input['dtend'] = (empty($input['dtend']) ? date() : $input['dtend'] );

// echo '<pre>';
// var_dump($input);
// exit;

$ics = new ICS($input);
echo $ics->to_string();
