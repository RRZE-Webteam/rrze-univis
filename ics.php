<?php

namespace RRZE\UnivIS;

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once $parse_uri[0] . 'wp-load.php';
include_once dirname(__FILE__) . '/includes/ICS.php';

$input = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

if (!empty($input['v']) && !empty($input['h']) && (hash('sha256', $input['v']) == $input['h']) && wp_verify_nonce($input['ics_nonce'], 'createICS')) {

    $aProps = json_decode(openssl_decrypt(base64_decode($input['v']), 'AES-256-CBC', hash('sha256', AUTH_KEY), 0, substr(hash('sha256', AUTH_SALT), 0, 16)), true);

    $ics = new ICS($aProps);

    // Output ICS
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $aProps['FILENAME'] . '.ics');
    echo $ics->toString();
} else {
    // Output Forbidden
    header('HTTP/1.0 403 Forbidden');
    echo 'The computer says "no".';
}

exit;