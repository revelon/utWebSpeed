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

// last file checked: 6575533

/*
and keywords_name not like '%lada hruska%' and keywords_name not like '%supernatural%' and keywords_name not like '%breaking bad%' 
and keywords_name not like '%walking dead%' and keywords_name not like '%arrow%' and keywords_name not like '%the vampire diaries%' 
and keywords_name not like '%jak jsem poznal vasi matku%' and keywords_name not like '%dragons defenders%' and keywords_name not like '%castle%' 
and keywords_name not like '%dragons riders%' and keywords_name not like '%two and a half men%' and keywords_name not like '%mentalist%' and keywords_name not like '%hockey%' 
and keywords_name not like '%californication%' and keywords_name not like '%jak vycvicit draky%' and keywords_name not like '%fringe%' and keywords_name not like '%tnm%' 
and keywords_name not like '%south park%' and keywords_name not like '%top gear%' and keywords_name not like '%red dwarf%' and keywords_name not like '%commercial%' 
and keywords_name not like '%chronicle%' and keywords_name not like '%star trek%' and keywords_name not like '%dva a pul chlapa%' and keywords_name not like '%dvbt%'
and keywords_name not like '%dva a pol chlapa%' and keywords_name not like '%misfits%' and keywords_name not like '%alcatraz%' and keywords_name not like '%dobrodruzny%' 
and keywords_name not like '%s01e0%' and keywords_name not like '%s02e0%' and keywords_name not like '%s03e0%' and keywords_name not like '%stargate%' 
and keywords_name not like '%s04e0%' and keywords_name not like '%s05e0%' and keywords_name not like '%s06e0%' and keywords_name not like '%cerveny trpaslik%' 
and keywords_name not like '%s01e1%' and keywords_name not like '%s02e2%' and keywords_name not like '%s03e2%' and keywords_name not like '%battlestar galactica%' 
and keywords_name not like '%s04e1%' and keywords_name not like '%s05e2%' and keywords_name not like '%s06e2%'  and keywords_name not like '%s03e1%' and keywords_name not like '%raising hope%' 
and keywords_name not like '%dark skies%' and keywords_name not like '%s02e2%' and keywords_name not like '%columbo%' and keywords_name not like '%hellsing%' 
and keywords_name not like '%dabing%' and keywords_name not like '%komedie%' and keywords_name not like '%hatsukoi%' and keywords_name not like '%episode%' 
and keywords_name not like '%mikrotik%' and keywords_name not like '%ipv6%' and keywords_name not like '%webinar%' and keywords_name not like '%youtube%' 
and keywords_name not like '%teorie velk%' and keywords_name not like '%big bang the%' and keywords_name not like '%official video%' and keywords_name not like '%naruto%' 
and keywords_name not like '% s01 e0%' and keywords_name not like '% s02 e0%' and keywords_name not like '% s03 e0%' and keywords_name not like '%stream cz%'
and keywords_name not like '%czdab%' and keywords_name not like '%horor%' and keywords_name not like '%ivysilani%' and keywords_name not like '%ceska televize%' 
and keywords_name not like '%ztracene svety%' and keywords_name not like '%heureka%' and keywords_name not like '%dokument%' and keywords_name not like '%tajemstvi divociny%' 
and keywords_name not like '%cone wars%' and keywords_name not like '%pribeh%' and keywords_name not like '%nepritel%' and keywords_name not like '%vesmir%' 
and keywords_name not like '%01x01%' and keywords_name not like '%01x02%' and keywords_name not like '%01x03%' and keywords_name not like '%01x04%' 
and keywords_name not like '%01x05%' and keywords_name not like '%01x06%' and keywords_name not like '%01x07%' and keywords_name not like '%01x08%' 
and keywords_name not like '%01x09%' and keywords_name not like '%01x10%' and keywords_name not like '%01x11%' and keywords_name not like '%01x12%' 
and keywords_name not like '%01x13%' and keywords_name not like '%01x14%' and keywords_name not like '%01x15%' and keywords_name not like '%01x16%' 
and keywords_name not like '%01x17%' and keywords_name not like '%01x18%' and keywords_name not like '%01x19%' and keywords_name not like '%01x20%' 
and keywords_name not like '%01x21%' and keywords_name not like '%01x22%' and keywords_name not like '%01x23%' and keywords_name not like '%01x24%' 
and keywords_name not like '%02x01%' and keywords_name not like '%02x02%' and keywords_name not like '%02x03%' and keywords_name not like '%02x04%' 
and keywords_name not like '%02x05%' and keywords_name not like '%02x06%' and keywords_name not like '%02x07%' and keywords_name not like '%02x08%' 
and keywords_name not like '%02x09%' and keywords_name not like '%02x10%' and keywords_name not like '%02x11%' and keywords_name not like '%02x12%' 
and keywords_name not like '%s1x01%' and keywords_name not like '%s1x02%' and keywords_name not like '%s1x03%' and keywords_name not like '%s1x04%' 
and keywords_name not like '%s1x05%' and keywords_name not like '%s1x06%' and keywords_name not like '%s1x07%' and keywords_name not like '%s1x08%' 
and keywords_name not like '%s1x09%' and keywords_name not like '%s1x10%' and keywords_name not like '%s1x11%' and keywords_name not like '%s1x12%' 
and keywords_name not like '%s1x13%' and keywords_name not like '%s1x14%' and keywords_name not like '%s1x15%' and keywords_name not like '%s1x16%' 
and keywords_name not like '%takovi normalni mimozemstane%' and keywords_name not like '%mortal kombat conquest%' and keywords_name not like '%jiste pane premiere%'  and keywords_name not like '%jiste pane ministre%' 
and keywords_name not like '%vsichni starostovi muzi%' and keywords_name not like '%season%'
*/


/* Select queries return a resultset */
if ($result = $mysqli->query("
SELECT *, lower(hex(hash)) hsh, f.id fid 
FROM admin_log_porn alp 
LEFT JOIN file_hashflags fh ON (alp.hash_id=fh.hashid)
LEFT JOIN file f ON (f.hashid=fh.hashid AND f.status='ok') 
LEFT JOIN file_origins fo ON (f.id=fo.file_id)
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
LEFT join uzivatele u ON (f.owner=u.id)
LEFT JOIN file_hash fhh ON (fhh.hashid=fh.hashid) 
WHERE displayStatus in ('maybe_illegal') AND (thumbSlideshowCount>0 or hasThumbImage=1) AND origin_file_id IS NULL 
AND cdnStatus='ok' AND banned=0 AND f.status='ok'
GROUP BY fh.hashid ORDER BY fh.hashid DESC LIMIT 20000
							 ")) {
/*

classic




gay





ban zoo




SELECT  *, lower(hex(hash)) hsh, f.id fid 
FROM file f LEFT JOIN file_upload_data fud ON (fud.file_id=f.id)
LEFT JOIN file_origins fo ON (f.id=fo.file_id)
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
LEFT JOIN file_hashflags fh USING (hashid)
LEFT join uzivatele u ON (f.owner=u.id)
LEFT JOIN file_hash USING (hashid) 
WHERE displayStatus in ('safe','maybe_safe') and (thumbSlideshowCount>0 or hasThumbImage) and (f.hashid between 80000000 and 100000000) 
and cdnStatus='ok' and banned<2 and f.status='ok' and contentType in ('video', 'image', 'archive') group by hashid order by fid desc limit 500;

SELECT  *, lower(hex(hash)) hsh, f.id fid 
FROM file f LEFT JOIN file_upload_data fud ON (fud.file_id=f.id)
LEFT JOIN user_agent ua ON (fud.uploader_useragent_id=ua.id)
LEFT JOIN file_origins fo ON (f.id=fo.file_id)
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
LEFT JOIN file_hashflags fh USING (hashid)
LEFT join uzivatele u ON (f.owner=u.id)
LEFT JOIN file_hash USING (hashid) 
LEFT JOIN file_mimetypes fm ON (fm.id=fh.mimetypeId)
LEFT JOIN file_description fd ON (f.id=fd.file_id)
WHERE contentType IN ('video', 'image', 'archive') AND origin_file_id IS NULL AND pornProbability=1 AND 
displayStatus IN ('porn', 'maybe_porn', 'maybe_illegal', 'illegal') AND f.hashid<117735331 
GROUP BY f.hashid ORDER BY fud.upload_date DESC LIMIT 1000;


SELECT  *, lower(hex(hash)) hsh, f.id fid 
FROM file f LEFT JOIN file_upload_data fud ON (fud.file_id=f.id)
LEFT JOIN user_agent ua ON (fud.uploader_useragent_id=ua.id)
LEFT JOIN file_origins fo ON (f.id=fo.file_id)
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
LEFT JOIN file_hashflags fh USING (hashid)
LEFT join uzivatele u ON (f.owner=u.id)
LEFT JOIN file_hash USING (hashid) 
LEFT JOIN file_mimetypes fm ON (fm.id=fh.mimetypeId)
LEFT JOIN file_description fd ON (f.id=fd.file_id)
WHERE contentType IN ('video', 'image', 'archive') AND origin_file_id IS NULL AND displayStatus='illegal' AND public='public' AND cdnStatus='ok' 
AND banned=2 GROUP BY f.hashid ORDER BY fud.upload_date DESC LIMIT 2000;

// obsah podle ID davek
SELECT *, lower(hex(hash)) hsh  
FROM upload_batch_file ubf 
JOIN file f ON (ubf.file_id=f.id) 
LEFT JOIN file_origins fo ON (f.id=fo.file_id) 
LEFT JOIN file_hash USING (hashid) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND contentType IN ('video', 'archive', 'image') 
AND displayStatus IN ('maybe_safe', 'safe') AND banned!=2 AND cdnStatus='ok' AND ubf.upload_batch_id IN (
1372191,
1699566
)
GROUP BY f.hashid
ORDER BY f.hashid DESC 
LIMIT 3000;

// neshody podle upload davek s nejakym pornem
SELECT *, lower(hex(hash)) hsh  
FROM file fx 
LEFT JOIN file_origins fo ON (fx.id=fo.file_id) 
LEFT JOIN file_hash USING (hashid)
LEFT JOIN file_hashflags fhh USING (hashid) 
LEFT JOIN file_flags ff ON (fx.id=ff.file_id) 
LEFT JOIN upload_batch_file ubfx ON (fx.id=ubfx.file_id)  
WHERE fo.origin_file_id IS NULL AND ff.thumbSlideshowCount>0 AND fhh.contentType IN ('video, archive') 
AND fhh.displayStatus IN ('safe', 'maybe_safe') AND fhh.banned=0 AND fx.status='ok' AND fhh.cdnStatus='ok' AND ubfx.upload_batch_id > 0 
AND ubfx.upload_batch_id IN (

select ubf.upload_batch_id davkaid 
from upload_batch_file ubf left join file f on (f.id=ubf.file_id) left join upload_batch ub on (ub.id=ubf.upload_batch_id) left join file_hashflags fh using (hashid) 
where ub.status='confirmed' group by ubf.upload_batch_id having sum(if(fh.displayStatus in ('porn','maybe_porn','maybe_illegal'),1,0)) > 0 and ((count(*) - sum(if(fh.displayStatus in ('porn','maybe_porn','maybe_illegal'),1,0))) > 0) and count(*) > 2 

) GROUP BY fx.hashid
ORDER BY fx.hashid ASC 
LIMIT 2500;


SELECT * 
FROM file f 
LEFT JOIN file_origins fo ON (id=file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND thumbSlideshowCount>0 AND contentType IN ('video', 'archive') 
AND displayStatus IN ('safe', 'maybe_safe') AND banned=0 AND status='ok' AND cdnStatus='ok' AND f.hashid>100000000
GROUP BY f.hashid
ORDER BY f.hashid DESC 
LIMIT 500;


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

// obecne neshody AI a lidi
SELECT * 
FROM file f 
LEFT JOIN file_origins fo ON (id=file_id) 
LEFT JOIN file_hashflags fh USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE origin_file_id IS NULL AND pornProbability=2 AND contentType IN ('video', 'archive', 'image') 
AND displayStatus IN ('maybe_safe', 'safe') AND banned=0 AND status='ok' AND cdnStatus='ok' 
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
            width: 90%;
            border: 1px dotted gray;}
        textarea {font-size: 9px}
        img {max-width:200px; max-height:200px}
        body {font-family: sans-serif; font-size: 11px}
        </style></head><body>';
	echo "Number of files to check: " . mysqli_num_rows($result) . "\n<style>img {display:inline-block}</style><hr>";

    $lastHashId = 0;
    //$quality = "640x360"; 
    $quality = "260x170";
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
            echo "<img src='{$thm}{$i}.jpg'></a>";
        }
        if ($row['contentType']==='image' && $row['hasThumbImage']) {
            echo "<a href=https://thumbs.uloz.to/{$row['hsh'][0]}/{$row['hsh'][1]}/{$row['hsh'][2]}/{$row['hsh']}.{$quality}.jpg download={$row['hsh']}.{$quality}.jpg>";
            echo "<img src=https://thumbs.uloz.to/{$row['hsh'][0]}/{$row['hsh'][1]}/{$row['hsh'][2]}/{$row['hsh']}.{$quality}.jpg></a>";
        }
        echo "<a target=_blank href=https://exec.uloz.to/support/files/file-preview?fileId={$row['fid']}>";        
        echo "<br>[{$row['hashid']}] {$row['name']}</a> pornProb={$row['pornProbability']}, porn%={$row['pornProbabilityImage']}, dispStatus={$row['displayStatus']}, 
        nameStat={$row['name_status']}, descStat={$row['description_status']}, PHC1={$row['pornHumanCheck1']}, PHC2={$row['pornHumanCheck2']}, PHC={$row['pornHumanCheck']}<br> 
        <button type='button' onclick='s(this, \"https://uloz.to/!{$row['slug']}/\")'>Straight</button> or 
        <button type='button' onclick='g(this, \"https://uloz.to/!{$row['slug']}/\")'>Gay</button> or 
        <button type='button' onclick='n(this, \"https://uloz.to/!{$row['slug']}/\")'>Normal</button> or 
        <button type='button' onclick='b(this, \"https://uloz.to/!{$row['slug']}/\")'>Ban</button> 
        ({$row['login']})<hr>";
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


