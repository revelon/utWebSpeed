<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit','2048M');


/*  
   Script to generate entries to all thumbnails we should have
   Mandatory is PHP source code of Uloz.to IntEncrypt library and access to ulozto db
*/

/* path to IntEncrypt library */
require ('./../ulozto-web/Nodus/Security/IntEncrypt.php');
/* path to DB configuration access file, in PHP format and array like this:
   $cnf = ['u' => "username", 'h' => "hostname", 'p' => "password", 'd' => "dbname" ]; */
require ('./../../cnf.php');







/* establish db connections */
$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if (is_object($mysqli) && $mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

fwrite(STDERR, "Progress: ");

/* iterate by millions, to 260, should cover everything
   downside of this process is that we are generating duplica images more times (minor issue) */
for ($lim=240; $lim<260; $lim++) {

    $q = "SELECT lower(hex(hash)) hsh, contentType, hasThumbImage, thumbSlideshowCount, f.id fid 
    FROM file f 
    LEFT JOIN file_flags ff ON (f.id=ff.file_id) 
    LEFT JOIN file_hashflags fh USING (hashid) 
    LEFT JOIN file_hash USING (hashid) 
    WHERE (thumbSlideshowCount>0 or hasThumbImage) AND (f.id BETWEEN ".($lim*1000000)." AND ".(($lim+1)*1000000).") AND 
    contentType IN ('image','archive','video','document') AND (upload_date between '2019-01-10 12:00:00' AND '2019-03-12 14:00:00')";
    /* Select queries return a resultset */
    if ($result = $mysqli->query($q)) {
        /* fetch associative array */
        while ($row = $result->fetch_assoc()) {
            /* print images */
            if ($row['contentType']==='image' && $row['hasThumbImage']) {
                echo "{$row['hsh']}\n";
            } else {
                /* print all videos, archives, documents */
                for ($i = 0; $i < $row['thumbSlideshowCount']; $i++) {
                    echo Nodus\Security\IntEncrypt::encrypt($row['fid'], 'Nodus') . ".{$i}\n";
                }
            }
        }
        /* be memory consumption aware */
        flush();
        gc_collect_cycles();
        /* progress indicator */
        fwrite(STDERR, "$lim ");
        /* free result set */
        $result->close();
    }

}

/* close resource */
$mysqli->close();

fwrite(STDERR, "\n\nDone\n");

