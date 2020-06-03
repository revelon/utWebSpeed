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

$f = file_get_contents("./thumbkeys.txt");
$data = explode(PHP_EOL, $f);

/*$data = [
'6ef2ZPM.6',
'b6803e3f989d41529c16805281b1fa3e',
'waC6Uai'
];*/

$md5s = [];
$fileIds = [];

foreach ($data as $row) {
	// skip empty or corrupted
	if (strlen(trim($row))<5) {
		continue;
	// md5 => img
	} elseif (strlen($row)>16) {
		$md5s[] = $row;
	// plain slideshow format without indexes
	} elseif (strpos($row, '.') === false) {
		$fileIds[] = $row;
	// other slideshow format with indexes
	} else {
		$fileIds[] = substr($row, 0, -2);
	}
}

var_dump(count($md5s), count($fileIds));

$md5s = array_unique($md5s);
$fileIds = array_unique($fileIds);

var_dump(count($md5s), count($fileIds));

/*
foreach ($md5s as $md5) {
    if ($result = $mysqli->query("select slug from file_hash left join file using (hashid) where hash=unhex('{$md5}')")) {
        $row = $result->fetch_assoc();
        if ($row['slug']) {
        	echo "http://uloz.to/!{$row['slug']}/obr\n";
        } else {
            echo "del {$md5}\n";
        }
    }
}


foreach ($fileIds as $fid) {
    if ($result = $mysqli->query("select slug from file where id=" . Nodus\Security\IntEncrypt::decrypt($fid, 'Nodus'))) {
        $row = $result->fetch_assoc();
        echo "http://uloz.to/file/{$row['slug']}/show\n";
    }
}
*/






$oper = ['video'=>'videoshot','archive'=>'archivepreview','document'=>'documentpreview','ebook'=>'documentpreview'];

$cntr = 0;
foreach ($fileIds as $fid) {
    if ($result = $mysqli->query("select id, contentType, status, cdnStatus, banned, slug, thumbSlideshowCount from file join file_flags on file.id=file_id 
    	left join file_hashflags using (hashid) where id=" . Nodus\Security\IntEncrypt::decrypt(substr($fid, 0, -2), 'Nodus'))) {
        $row = $result->fetch_assoc();
        if ($row['status']=='ok' and $row['cdnStatus']=='ok') {
        	file_put_contents('./to-regenerate-objects2.sql', "INSERT IGNORE INTO file_processor (file_id, action, priority) VALUES (".$row['id'] .", '".$oper[$row['contentType']]."', 10);\n", FILE_APPEND);
        } else {
        	$output = '';
        	for ($h = 0; $h<$row['thumbSlideshowCount']; $h++) $output .= $fid . "." . $h . "\n";
        	file_put_contents('./to-del-objects2.txt', $output, FILE_APPEND);
        }
    } else {
    	$output2 = '';
    	for ($h2 = 0; $h2<10; $h2++) $output2 .= $fid . "." . $h2 . "\n";
    	file_put_contents('./to-del-nonexistent-objects2.txt', $output2, FILE_APPEND);
    }
    echo $cntr++ . ",";
}


# hashe / obrazky only
$cntr = 0;
foreach ($md5s as $md5) {
    if ($result = $mysqli->query("select f.hashid hid, status, cdnStatus, banned from file_hash fh join file f on (f.hashid=fh.hashid and status='ok')  
        join file_hashflags using (hashid) where contentType='image' hash=unhex('{$md5}')")) {
        $row = $result->fetch_assoc();
        if ($row['status']=='ok' and $row['cdnStatus']=='ok' and $row['banned']==0) {
            file_put_contents('./to-regenerate-images.sql', "INSERT IGNORE INTO file_hash_processor (hashid, action, priority) VALUES (".$row['hid'] .", 'imagepreview', 10);\n", FILE_APPEND);
        } else {
            file_put_contents('./to-del-images.txt', "$md5\n", FILE_APPEND);
        }
    } else {
        file_put_contents('./to-del-nonexistent-images.txt', "$md5\n", FILE_APPEND);
    }
    echo $cntr++ . ",";
}

