<?php

ini_set('memory_limit','2048M');

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');
require ('/Users/xrevelon/cnfcdn.php');
$chunk = 2000;
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
echo "\nSrovnani hashu v obou DB po blocich za predpokladu ze nam sedi hashid\n\n";
echo "cdn_hash\tcdn_hashid\tcdn_status\tcdn_size\tut_hash_\tut_hashid\tut_status\tut_size\n";
for ($i=1; $i<13000; $i++) {
    $range = "" . ($i-1)*$chunk . " AND " . $i*$chunk;
    // get list of hashids
    $res = $mysqlicdn->query("SELECT GROUP_CONCAT(hashid SEPARATOR ',') hashids FROM file_hashcounter WHERE hashid BETWEEN " . $range);
    $r = $res->fetch_assoc();
    if ($result = $mysqlicdn->query("SELECT hashid, status, size, hash FROM file_hashcounter WHERE hashid BETWEEN " . $range . " ORDER BY hashid ASC")) {
        if ($result2 = $mysqli->query("SELECT hashid, IF(cdnStatus='deleted','del',cdnStatus) status, size, LOWER(HEX(hash)) hash FROM file_hashflags fh JOIN file_hash USING (hashid) WHERE fh.hashid IN (" . $r['hashids'] . ") ORDER BY fh.hashid ASC")) {
            while ($row = $result->fetch_assoc()) {



            $row = [1]; $row2 = [1];
            while (count($row) && count($row2)) {
                //var_dump($row, $row2, $r);
                if ($row['status'] !== $row2['status'] || $row['hashid'] !== $row2['hashid'] || $row['size'] !== $row2['size'] || $row['hash'] !== $row2['hash']) {
                    $rozdilu++;
                    //var_dump("Nesoulad CDN vs UT", $row, $row2, $r, "\n");
                    echo $row['hash'] . "\t" . $row['hashid'] . "\t" . $row['status'] . "\t" . $row['size'] . "\t" . $row2['hash'] . "\t" . $row2['hashid'] . "\t" . $row2['status'] . "\t" . $row2['size'] . "\n";
                }
                $row = $result->fetch_assoc();
                $row2 = $result2->fetch_assoc();
            }
        }
        if (is_object($result2)) $result2->close();
    }
    if (is_object($result)) $result->close();
    if (is_object($res)) $res->close();
    flush();
    gc_collect_cycles();
}

$mysqli->close();
$mysqlicdn->close();

echo "\n\n\nFinished s $rozdilu nesouladu!\n";
