<?php

// alternativni zpusob posilani dat: base64 enkodovany obrazek v dictionary {"image": obrazek} poslany jako POST

echo "\n\n\n";

$dir = '/Users/xrevelon/Downloads/batrla-nudity-fails2';

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
    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.farm.int.nds:8080/api/v1/adult_by_content");

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

Vzorek 4
Not nudes: 1007    Nudes: 171  Fails: 1

Finished


*/