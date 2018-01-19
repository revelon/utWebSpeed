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
SELECT * 
FROM file f 
LEFT JOIN file_origins fo ON (id=file_id) 
LEFT JOIN file_description fd ON (id=fd.file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND thumbSlideshowCount>0 and pornProbability=2 AND contentType IN ('video', 'archive', 'image') 
AND displayStatus IN ('maybe_porn', 'maybe_illegal', 'illegal') AND banned=0 AND status='ok' AND cdnStatus='ok' 
AND name_status in ('safe','') 
GROUP BY f.hashid
ORDER BY f.hashid DESC 
LIMIT 2000;
							 ")) {

/*

// porno dle scoringu na UT
SELECT * 
FROM file f 
LEFT JOIN file_origins fo ON (id=file_id) 
LEFT JOIN file_description fd ON (id=fd.file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND thumbSlideshowCount>0 and contentType IN ('video', 'archive', 'image') 
AND displayStatus IN ('maybe_safe', 'safe') AND banned=0 AND status='ok' AND cdnStatus='ok' AND name_status IN ('', 'porn', 'illegal') 
AND upload_date>'2017-08-31' 
GROUP BY f.hashid
ORDER BY f.hashid DESC 
LIMIT 2000;

//dotaz na neshody mezi AI
SELECT * 
FROM file f 
LEFT JOIN file_description fd ON (id=fd.file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE thumbSlideshowCount>0 AND 
((pornProbability=2 AND name_status='safe') OR (pornProbability=1 AND name_status IN ('porn', 'illegal'))) 
AND contentType IN ('video', 'archive', 'image') AND banned=0 AND status='ok' AND cdnStatus='ok'  
GROUP BY f.hashid
ORDER BY f.hashid DESC 
LIMIT 500;

// obecne neshody dobre funknci, mozna i duplicitni, ke kontrolam !!!!
SELECT * 
FROM file f 
LEFT JOIN file_origins fo ON (id=file_id) 
LEFT JOIN file_description fd ON (id=fd.file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND thumbSlideshowCount>0 and pornProbability=2 AND contentType IN ('video', 'archive', 'image') 
AND displayStatus IN ('maybe_porn', 'maybe_illegal', 'illegal') AND banned=0 AND status='ok' AND cdnStatus='ok' 
AND name_status in ('safe','') 
GROUP BY f.hashid
ORDER BY f.hashid DESC 
LIMIT 2000;

// vsechny nebezpecne zavery v nahledovych formatech
SELECT * 
FROM file f 
LEFT JOIN file_origins fo ON (id=file_id) 
LEFT JOIN file_description fd ON (id=fd.file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND thumbSlideshowCount>0 and pornProbability=2 AND contentType IN ('archive','video','image') 
AND displayStatus IN ('maybe_porn', 'maybe_illegal', 'illegal') AND banned=0 AND status='ok' AND cdnStatus='ok' 
GROUP BY f.hashid
ORDER BY id DESC 
LIMIT 1000;

// maybe_safe with pornProbability=1 pro archivy
SELECT * 
FROM file_hashflags fh 
LEFT JOIN file f USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE cdnStatus='ok' AND ff.thumbSlideshow!='' AND contentType='archive' AND banned=0 AND ff.thumbSlideshowCount>0 
AND f.status='ok' AND displayStatus='maybe_safe' AND pornProbability=1 
ORDER BY hashid DESC LIMIT 5000;

# dangerous only
SELECT *
FROM file_hashflags fh 
LEFT JOIN file f USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
LEFT JOIN file_description fd ON (f.id=fd.file_id) 
WHERE cdnStatus='ok' AND ff.thumbSlideshow!='' AND contentType='archive' AND banned=0 AND ff.thumbSlideshowCount>0 
AND f.status='ok' AND displayStatus IN ('safe', 'maybe_safe') AND (name_status IN ('illegal', 'porn') OR description_status IN ('illegal', 'porn'))
ORDER BY hashid DESC LIMIT 2000;

# all maybe safe
SELECT *
FROM file_hashflags fh 
LEFT JOIN file f USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE cdnStatus='ok' AND ff.thumbSlideshow!='' AND fh.length>1 AND contentType='archive' AND banned=0 AND ff.thumbSlideshowCount>0 
AND f.status='ok' AND displayStatus='maybe_safe' 
ORDER BY hashid DESC LIMIT 2000;
*/

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"/><style>button {cursor:crosshair}
        div.fixed {
            background-color: rgba(255,255,255,.7);
            position: fixed;
            bottom: 0;
            right: 0;
            width: 50%;
            border: 1px dotted gray;}
        textarea {font-size: 9px}
        img {max-width:300px; max-height:300px}
        body {font-family: sans-serif; font-size: 11px}
        </style></head><body>';
	echo "Number of files to check: " . mysqli_num_rows($result) . "\n<style>img {display:inline-block}</style><hr>";

    $lastHashId = 0;
    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        //var_dump($row);
        if ($row['hashid'] == $lastHashId) {
            continue; // skip potential duplicities
        }
    	$tiny = Nodus\Security\IntEncrypt::encrypt($row['id'], 'Nodus');
        $thm = "http://videoth.uloz.to/{$tiny[0]}/{$tiny[1]}/{$tiny[2]}/x{$tiny}.160x120.";
        echo "<a target=_blank href=https://exec.uloz.to/support/files/file-preview?fileId={$row['id']}>";
        for ($i = 0; $i < $row['thumbSlideshowCount']; $i++) {
        	echo "<img src='{$thm}{$i}.jpg' width=100>";
        }
        echo "<br>[{$row['hashid']}] {$row['name']}</a> pornProb={$row['pornProbability']}, porn%={$row['pornProbabilityImage']}, dispStatus={$row['displayStatus']}, 
        nameStat={$row['name_status']}, descStat={$row['description_status']}, PHC1={$row['pornHumanCheck1']}, PHC2={$row['pornHumanCheck2']}, PHC={$row['pornHumanCheck']} 
        &nbsp; <button type='button' onclick='s(this, \"https://uloz.to/!{$row['slug']}/\")'>Straight</button> or 
        <button type='button' onclick='g(this, \"https://uloz.to/!{$row['slug']}/\")'>Gay</button><hr>";
        $lastHashId = $row['hashid'];
    }

    echo "<hr>Latest hashid checked: {$lastHashId} <hr>";
    echo "<div class=fixed>Straight: <textarea id=straight rows=5 cols=28 onfocus=select()></textarea>
          Gay: <textarea id=gay rows=5 cols=28 onfocus=select()></textarea></div>
          <script>
            var straight = document.getElementById('straight');
            var gay = document.getElementById('gay');
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
           </script>";
    /* free result set */
    $result->close();
}





$mysqli->close();

echo "\n\n<hr>Finished\n";


