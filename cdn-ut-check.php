<?php

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');
require ('/Users/xrevelon/cnfcdn.php');
$chunk = 200000000;
echo "\n\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);
$mysqlicdn = new mysqli($cnfcdn['h'], $cnfcdn['u'], $cnfcdn['p'], $cnfcdn['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect UT failed: %s\n", $mysqli->connect_error);
    exit();
}
if ($mysqlicdn->connect_errno) {
    printf("Connect CDN failed: %s\n", $mysqlicdn->connect_error);
    exit();
}

echo "\nPocet OK hashu v obou DB po rozsazich o $chunk hashid\n\n";
for ($i=1; $i<2; $i++) {
    $range = "" . ($i-1)*$chunk . " AND " . $i*$chunk;
    $ut = -1; $cdn = -1;
    if ($result = $mysqli->query("SELECT count(*) pocet FROM file_hashflags WHERE cdnStatus='ok' AND hashid BETWEEN " . $range)) {
        $row = $result->fetch_assoc();
        $ut = $row['pocet'];
        $result->close();
    }
    if ($result = $mysqlicdn->query("SELECT count(*) pocet FROM file_hashcounter WHERE status='ok' AND hashid BETWEEN " . $range)) {
        $row = $result->fetch_assoc();
        $cdn = $row['pocet'];
        $result->close();
    }
    echo "diff = " . ($ut-$cdn) . " ut = $ut   cdn = $cdn  pro  $range \n";
    flush();
}

$mysqli->close();
$mysqlicdn->close();

echo "\n\nFinished\n";


