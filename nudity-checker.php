<?php

// alternativni zpusob posilani dat: base64 enkodovany obrazek v dictionary {"image": obrazek} poslany jako POST

echo "\n\n\n";

$dir = '/Users/xrevelon/Downloads/batrla-nudity-fails4';
//$dir = '/Users/xrevelon/Downloads/safes-to-check';

$files  = scandir($dir);
$res   = [];
$recall = array('fails' => 0, 'nude' => [], 'notnude' => []);

foreach ($files as $f) {
    if ($f[0] === '.') continue;
    /*
    $addr = "http://videoth.uloz.to/{$f[1]}/{$f[2]}/{$f[3]}/{$f}";
    echo "$addr $dir/$f\n";
    $addr = json_encode(['image' => $addr]);
    $a = shell_exec("curl -v -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.farm.int.nds:8080/api/v1/adult");
    */

    // alternativni pristup
    $addr = json_encode(['image' => base64_encode(file_get_contents($dir.'/'.$f))]);
    //$a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.farm.test.nds:8080/api/v1/adult_by_content");
    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://0.0.0.0:9999/api/v1/adult_by_content");

    echo "recall-api -> " . $a;
    $reply = json_decode($a);
    if (isset($reply->result)) {
        if ($reply->result->nude) $recall['nude'][] = $f;
        else $recall['notnude'][] = $f;
    } else {
        $recall['fails'] += 1;
    }
}

echo "\nRecall :"; var_dump($recall);

echo "\nNot nudes: " . sizeof($recall['notnude']) . "    Nudes: " . sizeof($recall['nude'])  .  "  Fails: "  . sizeof($recall['fails']);

echo "\n\nFinished\n";

/*

Vzorek 4 mixed
Old ver
Not nudes: 190    Nudes: 19
New ver
Not nudes: 53    Nudes: 156

Vzorek 3
New ver
Not nudes: 1001    Nudes: 179

Vzorek 2
New ver
Not nudes: 2240    Nudes: 384

Safes
New ver
Not nudes: 138    Nudes: 32
Old ver
Not nudes: 164    Nudes: 6

*/