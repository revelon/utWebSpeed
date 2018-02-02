<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"/><style>
        a {padding: 2px; margin: 2px;}
        a.safe{background-color: green}
        a.nude{background-color: purple}
        a.error{background-color: orange}
        a, img {max-width:300px; max-height:300px; display: inline-block;}
        body {font-family: sans-serif; font-size: 11px}
        </style></head><body>

<?php

# select suspicious files, download their file and send it to check, print results together with image

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');

echo "\n\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

/* Select queries return a resultset */
if ($result = $mysqli->query("
SELECT * 
FROM file f 
LEFT JOIN file_origins fo ON (id=file_id) 
LEFT JOIN file_description fd ON (id=fd.file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND thumbSlideshowCount>0 and pornProbability=2 AND contentType IN ('video', 'archive') 
AND status='ok' AND cdnStatus='ok' AND displayStatus IN ('porn', 'maybe_porn', 'illegal', 'maybe_illegal') and f.hashid<112383026
GROUP BY f.hashid
ORDER BY f.hashid DESC 
LIMIT 50;
")) {

    $lastHashId = 0;
    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        //var_dump($row);
        if ($row['hashid'] == $lastHashId) {
            continue; // skip potential duplicities
        }
        $tiny = Nodus\Security\IntEncrypt::encrypt($row['id'], 'Nodus');
        $thm = "http://videoth.uloz.to/{$tiny[0]}/{$tiny[1]}/{$tiny[2]}/x{$tiny}.640x360.";
        for ($i = 0; $i < $row['thumbSlideshowCount']; $i++) {
            $addr = json_encode(['image' => $thm.$i.'.jpg']);
            $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' http://nudity.farm.int.nds:8080/api/v1/adult");
            //echo "recall-api -> " . $a;
            $reply = json_decode($a);
            if (isset($reply->result)) {
                if ($reply->result->nude) continue;
            } else {
                echo "Err!!";
            }
            echo "<a href={$thm}{$i}.jpg download={$thm}{$i}.jpg class=safe>";
            echo "<img src='{$thm}{$i}.jpg' width=200></a>";
        }
        $lastHashId = $row['hashid'];
        flush();
    }
    echo "<hr>Latest hashid checked: {$lastHashId} <hr>";
    $result->close();
}

$mysqli->close();

?>
