<?php

# skriming table vs service bulk api comparison tool

require ('/Users/xrevelon/cnf.php');

echo "\n start at: " . date(DATE_RFC2822) . "\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

ini_set('memory_limit', '1024M');

// some basic configurations

// ulozto account with a lot of credit
$httpBasicCredentials = 'https://srw-test:srwtest123@';
// api authorization token, editable on CDN admin GUI
$authToken = 'd45068a3c2f948356b88222182b83807';
// project id
$project = 1;
// api base CDN URL path
$apiBase = 'https://service.ulozto.srw.cz/api/v1';
$apiBase2 = 'https://service.ulozto.srw.cz/api/v2';
// shared secret, should exists nowhere but in source configuration on both sides
define('STORAGE_SECRET', 'Krakonos666');
define('STORAGE_SECRET2', 'xeeb6eJ5cah7OoviaoPhi1eieHocha7v');

$limit = 500;
$do = true;
$command2 = "curl -H \"X-Auth-Token: {$authToken}\" '{$apiBase2}/files?limit={$limit}&offset=0'";

$deleted_ids = [];
$ok_ids = [];
$no_conversion_ids = [];

while ($do) {
    $ret2 = shell_exec($command2);
    $response2 = json_decode($ret2);
    var_dump('V2', $command2, 'deleted', count($deleted_ids), 'ok', count($ok_ids), 'not-ok', count($no_conversion_ids) /* , $response2, $ret2*/);
    $idList = [];
    $idHashMap = [];
    // structure: id, skrivy status, our status, note
    $output = "";

    foreach ($response2->data as $key => $obj) {
        //var_dump($key, $obj);
        if ($obj->deleted == 1) {
            $deleted_ids[] = $obj->id;
            $idHashMap[$obj->id] = ['status'=>'deleted', 'md5'=>''];
        } else {
            if ($obj->id && $obj->type == "video" && $obj->hasIssue == 0 && $obj->video->conversion[0]->uri) {
                $ok_ids[$obj->id] = $obj->md5;
                $idHashMap[$obj->id] = ['status'=>'ok', 'md5'=>$obj->md5];
            } else {
                $no_conversion_ids[$obj->id] = $obj->md5;
                $idHashMap[$obj->id] = ['status'=>'noconv', 'md5'=>$obj->md5];
            }
        }
        $idList[] = $obj->id;
    }

    $idsFromDb = [];
    /* Select queries return a resultset */
    if ($result = $mysqli->query("
        SELECT *, lower(hex(hash)) hsh 
        FROM file_hashflags fh 
        JOIN external_stream_file esf ON (fh.hashid=esf.file_hashid) 
        JOIN file_hash fhh ON (fhh.hashid=fh.hashid) 
        WHERE external_file_id IN (".implode(',',$idList).")")) {
        /* fetch associative array */
        while ($row = $result->fetch_assoc()) {
            echo "*";
            //var_dump($row);
            $idsFromDb[] = $row['external_file_id'];
            // matching row found
            if (isset($idHashMap[$row['external_file_id']])) {
                // skip deleted ones
                if ($idHashMap[$row['external_file_id']]['status']=='deleted' && $row['status']=='deleted') continue;
                // skip OK ones
                if ($idHashMap[$row['external_file_id']]['status']=='ok' && $row['status']=='ok' && 
                    $idHashMap[$row['external_file_id']]['md5']==$row['hsh']) continue;
                // skip incomplete ones
                if ($idHashMap[$row['external_file_id']]['status']=='noconv' && $row['status']=='incomplete') continue;

                $output .= $row['external_file_id'] . ", " . $idHashMap[$row['external_file_id']]['status'] .", " . $row['status'] . ", {$idHashMap[$row['external_file_id']]['md5']}:{$row['hsh']}\n";
            } else {
                $output .= $row['external_file_id'] . ", nonexist, " . $row['status'] . ", absurderror\n";
            }
        }
        $diff = array_diff($idsFromDb, $idList);
        foreach ($diff as $d) {
            $output .= $d . ", exist, notexist, delfromskrivy\n";
        }
        if ($output) file_put_contents('./xxx4diffstatus.csv', $output, FILE_APPEND);
    }

    $command2 = "curl -H \"X-Auth-Token: {$authToken}\" '{$response2->links->next}'";
    $do = ($response2->links->next) ? true : false;
}

file_put_contents('./xxx4deleted_ids.txt', print_r($deleted_ids, 1));
file_put_contents('./xxx4ok_ids.txt', print_r($ok_ids, 1));
file_put_contents('./xxx4not_ok_ids.txt', print_r($no_conversion_ids, 1));
