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
FROM file_hashflags fh 
LEFT JOIN file f USING (hashid) 
LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
WHERE cdnStatus='ok' AND ff.thumbSlideshow!='' AND fh.length>1 AND contentType='video' AND
f.status='ok' ORDER BY hashid DESC LIMIT 200;
							 ")) { // AND displayStatus IN ('maybe_safe', 'maybe_porn')

	echo "Number of files: " . mysqli_num_rows($result) . "\n<style>img {display:inline-block}</style><hr>";

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
    	$tiny = Nodus\Security\IntEncrypt::encrypt($row['id'], 'Nodus');
        $thm = "http://videoth.uloz.to/{$tiny[0]}/{$tiny[1]}/{$tiny[2]}/x{$tiny}.160x120.";
        for ($i = 0; $i < 10; $i++) {
        	echo "<img src='{$thm}{$i}.jpg' width=100>";
        }
        echo "<br>{$row['name']}<br>OLD:<a href='{$thm}0.jpg' 
            download='old_{$thm}0.jpg'>
            <img src='{$thm}0.jpg' border=1></a> 
            vs NEW:<a href='{$thm}{$row['thumbSlideshowIndex']}.jpg' 
            download='new_{$thm}{$row['thumbSlideshowIndex']}.jpg'>
            <img src='{$thm}{$row['thumbSlideshowIndex']}.jpg' border=1></a><hr>";
    }

    /* free result set */
    $result->close();
}





$mysqli->close();

echo "\n\nFinished\n";


