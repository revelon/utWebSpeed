<?php

# misc report tool

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');

echo "\n start at: " . date(DATE_RFC2822) . "\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$errors_ok = [];

$handle = fopen("./skrivy-oks.txt", "r");
while (($line = fgets($handle)) !== false) {
    if ($result = $mysqli->query('select external_file_id, status from external_stream_file where external_file_id='.trim($line))) {
        if ($row = $result->fetch_assoc()) {
            //var_dump($row, 11111);
            if ($row['status']!=='ok') {
                $errors_ok[] = $row;
                var_dump('ok', $row);
            } else echo ".";
            flush();
        }
        $result->close();
    }
}

$errors_del = [];

$handle = fopen("./skrivy-dels.txt", "r");
while (($line = fgets($handle)) !== false) {
    if ($result = $mysqli->query('select external_file_id, status from external_stream_file where external_file_id='.trim($line))) {
        if ($row = $result->fetch_assoc()) {
            //var_dump($row, 11111);
            if ($row['status']!=='del') {
                $errors_del[] = $row;
                var_dump('del', $row);
            } else echo ".";
            flush();
        }
        $result->close();
    }
}

$mysqli->close();

var_dump($errors_ok, $errors_del);

