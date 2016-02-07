<?php

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');

echo "\n\n\n";

// directories where hashes are stored
//$dir = '/Users/xrevelon/Documents/NudityFails4';
//$dir = '/Users/xrevelon/Documents/nudity-algorithm';
//$dir = '/Users/xrevelon/Documents/NudityFails3';
//$dir = '/Users/xrevelon/Documents/NudityFails5';
//$dir = '/Users/xrevelon/Documents/nudity-fails2';
//$dir = '/Users/xrevelon/Documents/NudityFails8';
//$dir = '/Users/xrevelon/Documents/NudityFails9';
//$dir = '/Users/xrevelon/Documents/NudityFails10-11';
//$dir = '/Users/xrevelon/Documents/NudityFails11.5';
//$dir = '/Users/xrevelon/Documents/NudityFails11.9';
//$dir = '/Users/xrevelon/Documents/NudityFails12';
//$dir = '/Users/xrevelon/Documents/NudityFailsUnsend12.5';
//$dir = '/Users/xrevelon/Documents/NudityFails12.6';
$dir = '/Users/xrevelon/Documents/NudityFails12.7';

$files  = scandir($dir);
$md5s   = [];
foreach ($files as $f) {
	if ($f[0] === '.') continue;
	$md5s[] = substr($f, 0, 32);
}
//var_dump($md5s);


$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

//select * from file_hash where binary hash = UPPER('a1e657333a500914529a968d85d67623')

//var_dump("select * from file_hash where hash in (UNHEX(UPPER('". implode("')),UNHEX(UPPER('", $md5s) ."')))");

/* Select queries return a resultset */
if ($result = $mysqli->query("select * from file_hash left join file using (hashid)
								where hash in (UNHEX(UPPER('". implode("')),UNHEX(UPPER('", $md5s) ."'))) and 
								status = 'ok'
							 ")) {

	echo "Number of files: " . mysqli_num_rows($result) . "\n";

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        //var_dump($row);
        echo "\nhttp://uloz.to/x" . Nodus\Security\IntEncrypt::encrypt($row['id'], 'Nodus') . "/";
    }

    /* free result set */
    $result->close();
}





$mysqli->close();

echo "\n\nFinished\n";


