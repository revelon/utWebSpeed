<?php

echo "\n\n\n";

$dir = '/Users/xrevelon/Documents/NudityFails12.9/quick';

$files  = scandir($dir);
$res   = [];
$ensemble = array('fails' => 0, 'nude' => [], 'notnude' => []);
$v2 = array('fails' => 0, 'nude' => [], 'notnude' => []);

foreach ($files as $f) {
    //if (sizeOf($ensemble['nude']) > 2) break; // debug option
	if ($f[0] === '.') continue;
	$md5 = substr($f, 0, 32);
    $addr = "https://imageth.uloz.to/{$md5[0]}/{$md5[1]}/{$md5[2]}/{$md5}.640x360.jpg";
    $addr = '"' . $addr . '"';
/*
    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' -H 'Authorization: Simple simELXZ6Dab23/2W+KD+e3zA7cr1' https://api.algorithmia.com/v1/algo/sfw/NudityDetectionEnsemble/0.2.7");
    echo "ens ->" . $a;
    $reply = json_decode($a);
    if (isset($reply->result)) {
        if ($reply->result->nude) $ensemble['nude'][] = $md5;
        else $ensemble['notnude'][] = $md5;
    } else {
        $ensemble['fails'] += 1;
    }
*/
    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' -H 'Authorization: Simple simELXZ6Dab23/2W+KD+e3zA7cr1' https://api.algorithmia.com/v1/algo/sfw/NudityDetectioni2v/0.1.x");
    echo "v2 ->" . $a;
    $reply = json_decode($a);
    if (isset($reply->result)) {
        if ($reply->result->nude) $v2['nude'][] = $md5;
        else $v2['notnude'][] = $md5;
    } else {
        $v2['fails'] += 1;
    }

}

//var_dump($res);

echo "\nV2 :"; var_dump($v2);

echo "\nEnsemble :"; var_dump($ensemble);

echo "\n\nFinished\n";


