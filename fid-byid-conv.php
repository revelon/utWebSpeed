<?php

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');

echo "\n\n\n";

$data = explode("\n", file_get_contents('./file-id-to-del.txt'));

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
								where id in (". implode(',', $data) .") and 
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


