<?php

namespace RRZE\UnivIS;


use RRZE\UnivIS\ICS;

// ICS
$probs = $_GET; // 2DO: sanitize/validate array


if ($probs){
    $ics = new ICS($props);
    echo 'header(\'Content-Type: text/calendar; charset=utf-8\');header(\'Content-Disposition: attachment; filename=invite.ics\')';
    echo $ics->to_string();
}
