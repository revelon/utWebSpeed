<?php
ini_set('memory_limit', '512M');
require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');

echo "\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$buffer = 0;
$success = 0;
$fail = 0;
$command = "";

function feedData($command) {
    $temp = tempnam('.', 'feeder_');
    file_put_contents($temp, $command);
    $rawResponse = shell_exec('curl -XPOST "http://localhost:9200/_bulk" --data-binary "@'.$temp.'"');
    //$rawResponse = shell_exec('curl -XPOST "http://es1.farm.int.nds:9200/_bulk" --data-binary "@'.$temp.'"');
    $response = json_decode($rawResponse);
    if ($response->errors === false) {
        echo "\nSuccess ";
        // tidy up the mess in current directory
        unlink($temp);
        return true;
    } else {
        echo "\nFail on {$temp} file with: " . $rawResponse;
        // do not unlink file to keep it for potential analysis
        return false;
    }
}

echo "\nExecuting SQL query...";
if ($result = $mysqli->query("
    select f.id, name, keywords_name as keywords, f.hashid, f.upload_date as uploaded, uploader_geoipcountry as geoipcountry, 
    contentType, fh.size as sizeInKB, if(displayStatus in ('safe', 'maybe_safe'),'UT','PF') as realm, rating_status as rating, 
    if(fhm.width>500 and fhm.height>500,'HQ','LQ') as quality, length as lengthInSec, 
    if(streamable,1,0) as streamable, if(thumbVideo='',false,true) as liveFreeStreaming, 
    if(archiveProtected>0 and password is not null,true,false) as archiveProtected, if(password,true,false) as passwordProtected 
    from file f
    left join file_hashflags fh using (hashid)
    left join file_hash_multimedia fhm using (hashid)
    left join file_upload_data fud on (f.id=fud.file_id)
    where status='ok' and public='public' and banned=0 
    and virusFound<2 and displayStatus not in ('illegal','maybe_illegal')
")) {

    $resultCount = mysqli_num_rows($result);
	echo "Query done, Number of files to index: " . $resultCount . "\n\n";

    while ($row = $result->fetch_assoc()) {
    	$command .= '{ "index" : { "_index" : "files3", "_type" : "public", "_id" : "' . $row['id'] . '" } }' . "\n";
        unset($row['id']);
        $command .= json_encode($row, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) . "\n";
        $buffer++;

        if (($buffer % 6666) === 0) {
            $ret = feedData($command);
            if ($ret) {
                $success++;
            } else {
                $fail++;
            }
            $buffer = 0;
            $command = "";
        }
    }

    if ($command) {
        $ret = feedData($command); // send the rest
        if ($ret) {
            $success++;
        } else {
            $fail++;
        }
    }

    $result->close();
} else {
    echo "\nDB query failed!";
}


$mysqli->close();

echo "\n\nFinished with OKs: {$success}   and   KOs: {$fail}  of batches... \n";


exit();

?>

DELETE /files3

GET /files3/_stats

PUT /files3
{
  "mappings": {
    "public": {
      "properties": {
        "name": {
            "type": "string",
            "index": "no"
        },
        "keywords": {
            "type": "string"
        },
        "hashid": {
            "type": "long"
        },
        "uploaded": {
          "type":   "date",
          "format": "yyy-MM-dd HH:mm:ss"
        },
        "geoipcountry": {
            "type": "string"
        },
        "contentType": {
            "type": "string"
        },
        "sizeInKB": {
            "type": "long"
        },
        "realm": {
            "type": "string"
        },
        "rating": {
            "type": "short"
        },
        "quality":   {
            "type": "string"
        },
        "lengthInSec": {
            "type": "integer"
        },
        "streamable": {
            "type": "boolean"
        },
        "liveFreeStreaming": {
            "type": "boolean"
        },
        "passwordProtected": {
            "type": "boolean"
        }
      }
    }
  }
}
