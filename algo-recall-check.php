<?php

echo "\n\n\n";

$dir = '/Users/xrevelon/Documents/Nudities/algo-fail-marked-as-nudities';
$dir = '/Users/xrevelon/Documents/Nudities/algo-success-marked-as-not-nude';
//$dir = '/Users/xrevelon/Documents/Nudities/algo-success-marked-as-nudities';

$files  = scandir($dir);
$res   = [];
$recall = array('fails' => 0, 'nude' => [], 'notnude' => []);
$v2 = array('fails' => 0, 'nude' => [], 'notnude' => []);

foreach ($files as $f) {
    //if (sizeOf($ensemble['nude']) > 2) break; // debug option
	if ($f[0] === '.') continue;
	$md5 = substr($f, 0, 32);
    $addr = "https://imageth.uloz.to/{$md5[0]}/{$md5[1]}/{$md5[2]}/{$md5}.640x360.jpg";
    //$addr = '"' . $addr . '"';
    $addr = json_encode(['image' => $addr]);


//curl -X POST -d 'https://videoth.uloz.to/i/K/U/xiKUTXYh.640x360.8.jpg' -H 'Content-Type: application/json' http://nudity1.farm.int.nds:8080/api/v1/adult

//curl -XPOST -d'{"image":"https://videoth.uloz.to/i/J/U/xiJU4pcE.640x360.2.jpg"}' -H "Content-Type: application/json"  http://nudity1.farm.int.nds:8080/api/v1/adult

    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.farm.int.nds:8080/api/v1/adult");
    echo "recall-api -> " . $a;
    $reply = json_decode($a);
    if (isset($reply->result)) {
        if ($reply->result->nude) $recall['nude'][] = $md5;
        else $recall['notnude'][] = $md5;
    } else {
        $recall['fails'] += 1;
    }

/*
    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' -H 'Authorization: Simple simELXZ6Dab23/2W+KD+e3zA7cr1' https://api.algorithmia.com/v1/algo/sfw/NudityDetectioni2v/0.2.3");
    echo "algo-v2 -> " . $a;
    $reply = json_decode($a);
    if (isset($reply->result)) {
        if ($reply->result->nude) $v2['nude'][] = $md5;
        else $v2['notnude'][] = $md5;
    } else {
        $v2['fails'] += 1;
    }
*/
}

//var_dump($res);

echo "\nV2 :"; var_dump($v2);

echo "\nRecall :"; var_dump($recall);

echo "\n\nFinished\n";


