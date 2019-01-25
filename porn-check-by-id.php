<?php

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
SELECT  *, lower(hex(hash)) hsh, f.id fid 
FROM file f 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
LEFT JOIN file_hashflags fh USING (hashid)
LEFT join uzivatele u ON (f.owner=u.id)
LEFT JOIN file_hash USING (hashid) 
WHERE displayStatus IN ('maybe_porn', 'illegal', 'maybe_illegal', 'maybe_safe') AND (thumbSlideshowCount>0 or hasThumbImage) 
AND contentType!='image' AND (pornProbability=2 OR name_status IN ('porn', 'illegal')) AND banned<2 AND f.status IN ('ok', 'trash', 'incomplete')
GROUP BY hashid LIMIT 400
"
/*"
SELECT  *, lower(hex(hash)) hsh, f.id fid 
FROM file f LEFT JOIN file_upload_data fud ON (fud.file_id=f.id)
LEFT JOIN file_origins fo ON (f.id=fo.file_id)
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
LEFT JOIN file_hashflags fh USING (hashid)
LEFT join uzivatele u ON (f.owner=u.id)
LEFT JOIN file_hash USING (hashid) 
WHERE thumbSlideshowCount>0 AND f.id IN (
194997,
904035,
242056
807118,
264820,
1005517,
1017536,
497508,
1119593,
1137583
) group by hashid order by fid desc limit 5000;"*/)) {

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"/><style>button {cursor:crosshair}
        div.fixed {
            background-color: rgba(255,255,255,.7);
            position: fixed;
            bottom: 0;
            right: 0;
            width: 90%;
            border: 1px dotted gray;}
        button {height: 25px;}
        textarea {font-size: 9px}
        img {max-width:300px; max-height:300px}
        body {font-family: sans-serif; font-size: 11px}
        </style></head><body>';
	echo "Number of files to check: " . mysqli_num_rows($result) . "\n<style>img {display:inline-block}</style><hr>";

    $lastHashId = 0;
    $quality = "640x360"; 
    //$quality = "160x120";
    $all = "";
    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        //var_dump($row);
        if ($row['hashid'] == $lastHashId) {
            continue; // skip potential duplicities
        }
    	$tiny = Nodus\Security\IntEncrypt::encrypt($row['fid'], 'Nodus');
        $thm = "https://thumbs.uloz.to/{$tiny[0]}/{$tiny[1]}/{$tiny[2]}/x{$tiny}.{$quality}.";
        for ($i = 0; $i < $row['thumbSlideshowCount']; $i++) {
            echo "<a href={$thm}{$i}.jpg download={$thm}{$i}.jpg class=safe>";
            echo "<img src='{$thm}{$i}.jpg' width=220></a>";
        }
        if ($row['contentType']==='image' && $row['hasThumbImage']) {
            echo "<a href=https://thumbs.uloz.to/{$row['hsh'][0]}/{$row['hsh'][1]}/{$row['hsh'][2]}/{$row['hsh']}.{$quality}.jpg download={$row['hsh']}.{$quality}.jpg>";
            echo "<img src=https://thumbs.uloz.to/{$row['hsh'][0]}/{$row['hsh'][1]}/{$row['hsh'][2]}/{$row['hsh']}.{$quality}.jpg width=220></a>";
        }
        echo "<a target=_blank href=https://exec.uloz.to/support/files/file-preview?fileId={$row['fid']}>";        
        echo "<br>[{$row['hashid']}] {$row['name']}</a> pornProb={$row['pornProbability']}, porn%={$row['pornProbabilityImage']}, dispStatus={$row['displayStatus']}, 
        nameStat={$row['name_status']}, descStat={$row['description_status']}, PHC1={$row['pornHumanCheck1']}, PHC2={$row['pornHumanCheck2']}, PHC={$row['pornHumanCheck']}<br> 
        <button type='button' onclick='s(this, \"https://uloz.to/!{$row['slug']}/\")'>Straight</button> or 
        <button type='button' onclick='g(this, \"https://uloz.to/!{$row['slug']}/\")'>Gay</button> or 
        <button type='button' onclick='n(this, \"https://uloz.to/!{$row['slug']}/\")'>Normal</button> or 
        <button type='button' onclick='b(this, \"https://uloz.to/!{$row['slug']}/\")'>Ban</button><hr>";
        $lastHashId = $row['hashid'];
        $all .= "https://uloz.to/!{$row['slug']}/\n";
    }


    echo "<hr>Latest hashid checked: {$lastHashId} <hr><br><br><br><br><br><br>";
    echo "<div class=fixed>Straight: <textarea id=straight rows=5 cols=28 onfocus=select()></textarea>
          Gay: <textarea id=gay rows=5 cols=28 onfocus=select()></textarea>
          Normal: <textarea id=normal rows=5 cols=28 onfocus=select()></textarea>
          Ban: <textarea id=ban rows=5 cols=28 onfocus=select()></textarea>
          All: <textarea rows=5 cols=28 onfocus=select()>".$all."</textarea>
          </div>
          <script>
            var straight = document.getElementById('straight');
            var gay = document.getElementById('gay');
            var norm = document.getElementById('normal');
            var ban = document.getElementById('ban');
            function s(el, url) {
                //console.log(el.dataset.straight);
                if (el.dataset.straight != 1) {
                    el.dataset.straight = 1;
                    straight.value = straight.value + url + \"\\n\";
                } else {
                    el.dataset.straight = 0;
                    straight.value = ('' + straight.value).replace(url + \"\\n\", '');
                }
            }
           function g(el, url) {
                if (el.dataset.gay != 1) {
                    el.dataset.gay = 1;
                    gay.value = gay.value + url + \"\\n\";
                } else {
                    el.dataset.gay = 0;
                    gay.value = ('' + gay.value).replace(url + \"\\n\", '');
                }
            }
            function b(el, url) {
                if (el.dataset.ban != 1) {
                    el.dataset.ban = 1;
                    ban.value = ban.value + url + \"\\n\";
                } else {
                    el.dataset.ban = 0;
                    ban.value = ('' + ban.value).replace(url + \"\\n\", '');
                }
            }
           function n(el, url) {
                if (el.dataset.norm != 1) {
                    el.dataset.norm = 1;
                    norm.value = norm.value + url + \"\\n\";
                } else {
                    el.dataset.gay = 0;
                    norm.value = ('' + norm.value).replace(url + \"\\n\", '');
                }
            }

           </script>";
    /* free result set */
    $result->close();
}





$mysqli->close();

echo "\n\n<hr>Finished\n";


