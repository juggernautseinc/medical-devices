<?php

/*
 *  package   Comlink OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

use OpenEMR\Common\Crypto\CryptoGen;

function curl_get_content($url, $type = "GET", $payload = '', $redirectURL = false)
{
    $cryptoGen = new CryptoGen();
    $x_username = $GLOBALS['comlink_username'];
    $x_password = $cryptoGen->decryptStandard($GLOBALS['comlink_password']);
    $x_orgid = $GLOBALS['comlink_xorgid'];

    set_time_limit(60);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "x-username: $x_username",
        "x-password: $x_password",
        "x-orgid: $x_orgid"

    ));

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($response === FALSE) {
        printf(
            "cUrl error (#%d): %s<br>\n",
            curl_errno($ch),
            htmlspecialchars(curl_error($ch))
        );
    }
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    curl_close($ch);
    if ($redirectURL) {
        return $info['redirect_url'];
    } else {
        return $response;
    }
}
