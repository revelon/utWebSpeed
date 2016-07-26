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
SELECT name, slug, hashid, lower(hex(hash)) hsh
FROM file_hashflags fh 
LEFT JOIN file_hash USING (hashid)
LEFT JOIN file f USING (hashid) 
LEFT JOIN file_description fd ON (f.id=fd.file_id) 
WHERE cdnStatus='ok' AND contentType='image' AND banned=0 
AND f.status='ok' AND displayStatus = 'safe' AND (name_status IN ('porn', 'illegal') OR description_status IN ('porn', 'illegal'))
ORDER BY hashid DESC LIMIT 200;
							 ")) {

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"/><style>
        body {-moz-column-count: 2; -webkit-column-count: 2; column-count: 2}
        button {cursor:crosshair}
        div.fixed {
            background-color: rgba(255,255,255,.7);
            position: fixed;
            bottom: 0;
            right: 0;
            width: 50%;
            border: 1px dotted gray;
        }
        a img {width: 100px; max-height: 200px;}
        a img:hover {width: 200px;}
        </style></head><body>';
	echo "Number of files to check: " . mysqli_num_rows($result) . "\n<style>img {display:inline-block}</style><hr>";

    $lastHashId = 0;
    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        //var_dump($row);
        $thm = "http://imageth.uloz.to/{$row['hsh'][0]}/{$row['hsh'][1]}/{$row['hsh'][2]}/{$row['hsh']}.640x360.jpg";
        echo "<a target=_blank href=https://uloz.to/!{$row['slug']}/><img src=$thm>";
        echo "<br>{$row['name']}</a> &nbsp; <button type='button' onclick='s(this, \"https://uloz.to/!{$row['slug']}/\")'>Straight</button> or 
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


