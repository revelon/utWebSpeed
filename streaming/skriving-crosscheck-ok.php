<?php

# skrining db vs service comparison tool

require ('/Users/xrevelon/cnf.php');

echo "\n start at: " . date(DATE_RFC2822) . "\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

ini_set('memory_limit', '1024M');

// some basic configurations

// ulozto account with a lot of credit
$httpBasicCredentials = 'https://srw-test:srwtest123@';
// api authorization token, editable on CDN admin GUI
$authToken = 'd45068a3c2f948356b88222182b83807';
// project id
$project = 1;
// api base CDN URL path
$apiBase = 'https://service.ulozto.srw.cz/api/v1';
$apiBase2 = 'https://service.ulozto.srw.cz/api/v2';
// shared secret, should exists nowhere but in source configuration on both sides
define('STORAGE_SECRET', 'Krakonos666');
define('STORAGE_SECRET2', 'xeeb6eJ5cah7OoviaoPhi1eieHocha7v');


$fails1 = [];
$oks1 = [];
$fails2 = [];
$oks2 = [];


if ($result = $mysqli->query("select * from external_stream_file where status='incomplete' order by id asc")) {
    while ($row = $result->fetch_assoc()) {

        $command2 = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase2}/files/" . $row['external_file_id'];
        $ret2 = shell_exec($command2);
        $response2 = json_decode($ret2);
        var_dump('V2', $response2, $ret2);
        if ($response2->hasIssue || !$response2->video->conversion[0]->uri) {
            $fails2[$row['external_file_id']] = $response2;
        } else {
            $oks2[] = $row['external_file_id'];
            file_put_contents('./v2-ok-ids.txt', $row['external_file_id']."\n", FILE_APPEND);
        }
    }
    $result->close();
}

$mysqli->close();

var_dump("V2 fails", count($fails2));

file_put_contents('./bxxxv2fails.txt', print_r($fails2, 1));
file_put_contents('./bxxxv2oks.txt', print_r($oks2, 1));
