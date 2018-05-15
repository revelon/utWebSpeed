<?php

ini_set('memory_limit','2048M');

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');
require ('/Users/xrevelon/cnfcdn.php');
$chunk = 10000;
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

$rozdilu = 0;
echo "\nSrovnani hashu v obou DB po radcich dle hashid a md5\n\n";
echo "hash\tcdn_hashid\tcdn_status\tcdn_size\tut_hashid\tut_status\tut_size\n";
for ($i=1; $i<1200; $i++) {
    $range = "" . ($i-1)*$chunk . " AND " . $i*$chunk;
    if ($result = $mysqlicdn->query("SELECT hashid, status, size, hash FROM file_hashcounter WHERE hashid BETWEEN " . $range)) {
        while ($row = $result->fetch_assoc()) {
            if ($result2 = $mysqli->query("SELECT hashid, IF(cdnStatus='deleted','del',cdnStatus) status, size FROM file_hashflags JOIN file_hash USING (hashid) WHERE hash=UNHEX('" . $row['hash'] . "')")) {
                $row2 = $result2->fetch_assoc();
                if ($row['status'] !== $row2['status'] || $row['hashid'] !== $row2['hashid'] || $row['size'] !== $row2['size']) {
                    $rozdilu++;
                    //var_dump("Nesoulad CDN vs UT", $row, $row2, "\n");
                    echo $row['hash'] . "\t" . $row['hashid'] . "\t" . $row['status'] . "\t" . $row['size'] . "\t" . $row2['hashid'] . "\t" . $row2['status'] . "\t" . $row2['size'] . "\n";
                }
                $result2->close();
            }
        }
    }
    if (is_object($result)) $result->close();
    flush();
}


$mysqli->close();
$mysqlicdn->close();

echo "\n\n\nFinished s $rozdilu nesouladu!\n";


