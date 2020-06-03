<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit','2048M');


/*  
   Script to generate entries to all thumbnails we should have
   Mandatory is PHP source code of Uloz.to IntEncrypt library and access to ulozto db
   Optimized to avoid duplicates on images
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
for ($lim=0; $lim<262; $lim++) {

    $q = "SELECT thumbSlideshowCount, file_id 
    FROM file_flags
    WHERE thumbSlideshowCount>0 AND (file_id BETWEEN ".($lim*1000000)." AND ".(($lim+1)*1000000).")";
    /* Select queries return a resultset */
    if ($result = $mysqli->query($q)) {
        /* fetch associative array */
        while ($row = $result->fetch_assoc()) {
            /* print all videos, archives, documents */
            for ($i = 0; $i < $row['thumbSlideshowCount']; $i++) {
                echo Nodus\Security\IntEncrypt::encrypt($row['file_id'], 'Nodus') . ".{$i}\n";
            }
        }
        /* free result set */
        $result->close();
    }

    $q = "SELECT lower(hex(hash)) hsh  
    FROM file_hashflags fh 
    LEFT JOIN file_hash USING (hashid) 
    WHERE hasThumbImage=1 AND (fh.hashid BETWEEN ".($lim*1000000)." AND ".(($lim+1)*1000000).") AND contentType='image'";
    /* Select queries return a resultset */
    if ($result = $mysqli->query($q)) {
        /* fetch associative array */
        while ($row = $result->fetch_assoc()) {
            /* print images */
            echo "{$row['hsh']}\n";
        }
        /* free result set */
        $result->close();
    }

    /* be memory consumption aware */
    flush();
    gc_collect_cycles();
    /* progress indicator */
    fwrite(STDERR, "$lim ");

}

/* close resource */
$mysqli->close();

fwrite(STDERR, "\n\nDone\n");

