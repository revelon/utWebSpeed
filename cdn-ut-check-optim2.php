<?php

ini_set('memory_limit','2048M');

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');
require ('/Users/xrevelon/cnfcdn.php');
$chunk = 4000;
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

// extend groupconcat capacity
$mysqlicdn->query("SET SESSION group_concat_max_len = 1000000");

$rozdilu = 0;
echo "\nSrovnani hashu v obou DB po blocich za predpokladu ze nam sedi hashid, majici hashida\n\n";
echo "cdn_hash\tcdn_hashid\tcdn_status\tcdn_size\tut_hash_\tut_hashid\tut_status\tut_size\n";
for ($i=0; $i<36000; $i++) {
    $range = "" . ($i-1)*$chunk . " AND " . $i*$chunk;
    // get list of hashids
    $res = $mysqlicdn->query("SELECT GROUP_CONCAT(hashid SEPARATOR ',') hashids FROM file_hashcounter WHERE hashid BETWEEN " . $range);
    if (is_object($res)) $r = $res->fetch_assoc();
    else continue;
    $cdn = []; $ut = []; $row = []; $row2 = [];
    $result = $mysqlicdn->query("SELECT hashid, status, size, hash FROM file_hashcounter WHERE hashid IN (" . $r['hashids'] . ") ORDER BY hashid ASC");
    $result2 = $mysqli->query("SELECT hashid, IF(cdnStatus='deleted','del',cdnStatus) status, size, LOWER(HEX(hash)) hash FROM file_hashflags fh JOIN file_hash USING (hashid) WHERE fh.hashid IN (" . $r['hashids'] . ") ORDER BY fh.hashid ASC");
    while (is_object($result) && $row = $result->fetch_assoc()) $cdn[$row['hashid']] = $row;
    while (is_object($result2) && $row2 = $result2->fetch_assoc()) $ut[$row2['hashid']] = $row2;
    foreach ($cdn as $hashid => $data) {
        //echo $data['hash'] . "\t" . $data['hashid'] . "\t" . $data['status'] . "\t" . $data['size'] . "\t" . $ut[$hashid]['hash'] . "\t" . $ut[$hashid]['hashid'] . "\t" . $ut[$hashid]['status'] . "\t" . $ut[$hashid]['size'] . "\n";
        if ($data['status'] !== $ut[$hashid]['status'] || $data['hashid'] !== $ut[$hashid]['hashid'] || $data['size'] !== $ut[$hashid]['size'] || $data['hash'] !== $ut[$hashid]['hash']) {
            $rozdilu++;
            echo $data['hash'] . "\t" . $data['hashid'] . "\t" . $data['status'] . "\t" . $data['size'] . "\t" . $ut[$hashid]['hash'] . "\t" . $ut[$hashid]['hashid'] . "\t" . $ut[$hashid]['status'] . "\t" . $ut[$hashid]['size'] . "\n";
        }
    }
    if (is_object($result2)) $result2->close();
    if (is_object($result)) $result->close();
    if (is_object($res)) $res->close();
    fwrite(STDERR, "iterate $range \n");
    flush();
    gc_collect_cycles();
}

$mysqli->close();
$mysqlicdn->close();

echo "\n\n\nFinished s $rozdilu nesouladu!\n";
