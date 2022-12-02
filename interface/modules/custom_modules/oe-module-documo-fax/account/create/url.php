<?php

require_once dirname(__FILE__, 6) . "/globals.php";

$sql = "select account_user from documo_user";
$response = sqlQuery($sql);

echo "<pre>";
//var_dump($response['account_user']);
$uuid = json_decode($response['account_user'], true);
var_dump($uuid);
echo $uuid['uuid'];
