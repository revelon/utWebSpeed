<?php

// alternativni zpusob posilani dat: base64 enkodovany obrazek v dictionary {"image": obrazek} poslany jako POST

echo "\n\n\n";

//$dir = '/Users/xrevelon/Downloads/batrla-nudity-fails4';
//$dir = '/Users/xrevelon/Downloads/safes-to-check';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/positive-porns';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/elder-nudes';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/undetected-porns';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/elder-safes';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/porns-17-10-2018';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/safes-17-10-2018';
$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/product-1810-sample-nude';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/product-1810-sample-not-nude';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/product-sample-unor-2019-safe';
//$dir = '/Users/xrevelon/Downloads/porns-sample-09-2018/product-sample-unor-2019-nude';
//$dir = "/Users/xrevelon/Downloads/porns-sample-09-2018/ut-safe-set";
//$dir = "/Users/xrevelon/Downloads/porns-sample-09-2018/ut-nude-set";


$files  = scandir($dir);
$res   = [];
$recall = array('fails' => [], 'nude' => [], 'notnude' => []);
$counter = 1;
echo "scanning {$dir}:\n";

foreach ($files as $f) {
    if ($f[0] === '.') continue;
    /*
    $addr = "http://videoth.uloz.to/{$f[1]}/{$f[2]}/{$f[3]}/{$f}";
    echo "$addr $dir/$f\n";
    $addr = json_encode(['image' => $addr]);
    $a = shell_exec("curl -v -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.farm.int.nds:8080/api/v1/adult");
    */

    // alternativni pristup
    //$addr = json_encode(['image' => "data:image/jpeg;base64," . base64_encode(file_get_contents($dir.'/'.$f))]); // nsfv
    $addr = json_encode(['image' => base64_encode(file_get_contents($dir.'/'.$f))]); // nudity
    //$a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.farm.test.nds:8080/api/v1/adult_by_content");
    //$a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://0.0.0.0:8081/api/v1/adult");
    //$a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://0.0.0.0:8080/api/v1/adult_by_content");
    //$a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity1.dev1.ci:8080/api/v1/adult_by_content");
    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.stage.nub:8080/api/v1/adult_by_content");

    echo $counter++ . ". recall-api -> " . $a;
    //var_dump($a, $recall);
    $reply = json_decode($a);
    if (isset($reply->result)) { // nudity_deteckce
        if ($reply->result->nude) $recall['nude'][] = $f;
        else $recall['notnude'][] = $f;
    /*if (isset($reply->class_name)) { // nsfv
        if ($reply->class_name=="nude") $recall['nude'][] = $f;
        else $recall['notnude'][] = $f;*/
    } else {
        $recall['fails'][] = $f;
    }
}

echo "\nRecall :"; var_dump($recall);

echo "\nNot nudes: " . sizeof($recall['notnude']) . "    Nudes: " . sizeof($recall['nude'])  .  "  Fails: "  . sizeof($recall['fails']);

echo "\n\nFinished\n";
