<?php

namespace RRZE\UnivIS;

include_once(dirname(__FILE__) . '/includes/ICS.php');

$input = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

$aFreq = [
    'woche' => 'WEEKLY',
    'monat' => 'MONTHLY',
    'jahr' => 'YEARLY',
    'jede' => 'INTERVAL=1',
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
    $input['repeat'] = implode(',', array_intersect($aDay, str_replace(array_keys($aDay), array_values($aDay), preg_split('/(\,| )/', strtolower($input['repeat'])))));
    if (empty($input['freq'])){
        $input['freq'] = 'WEEKLY;INTERVAL=1';
    }
}

$ics = new ICS($input);

// Output
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $input['filename'] . '.ics');
echo $ics->toString();
