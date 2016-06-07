<?php

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');

echo "\n\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

/* Select queries return a resultset */
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
where id > 108774660 and status='ok' and public='public' and banned=0 
and virusFound<2 and displayStatus not in ('illegal','maybe_illegal')
limit 10000;

							 ")) {
	echo "Number of files: " . mysqli_num_rows($result);
    /* fetch associative array */

    while ($row = $result->fetch_assoc()) {
        $command = "curl -XPUT \"http://localhost:9200/files/public/{$row['id']}\" -d'";
    	unset($row['id']);
        $command .= json_encode($row) . "'";
        echo "\n\n" . $command;
        $res = shell_exec($command);
        $response = json_decode($res);
        if ($response->created === true) {
            echo "\nsuccess";
        } else {
            var_dump('failed', $response);
        }
    }

    /* free result set */
    $result->close();
}





$mysqli->close();

echo "\n\nFinished\n";


exit();

?>

DELETE files

GET /files/_stats

PUT files
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
            "type": "integer"
        },
        "realm": {
            "type": "string"
        },
        "rating": {
            "type": "integer"
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


POST /files/_search
{
    "query": {
        "query_string": {
            "query": "petr rar",
            "fields": ["keywords"]
        }
    },
    "filter": {
        "term": {
            "passwordProtected": false 
        }
    },
    "from" : 0, "size" : 10,
    "sort" : [
        {"uploaded" : "desc"}
   ]
}


POST /files/_search
{
    "match": {
        "keywords": "petr rar",
        "operator" : "and"
    },
    "from" : 0, "size" : 10
}


// boost limit strict search

// dodat i file_category!!! nejak

select f.id, name, keywords_name as keywords, f.hashid, f.upload_date as uploaded, uploader_geoipcountry as geoipcountry, 
contentType, fh.size as sizeInKB, if(displayStatus in ('safe', 'maybe_safe'),'UT','PF') as realm, rating_status as rating, 
if(fhm.width>500 and fhm.height>500,'HQ','LQ') as quality, length as lengthInSec, 
if(streamable,1,0) as streamable, if(thumbVideo='',false,true) as liveFreeStreaming, 
if(archiveProtected>0 and password is not null,true,false) as archiveProtected, if(password,true,false) as passwordProtected 
from file f
left join file_hashflags fh using (hashid)
left join file_hash_multimedia fhm using (hashid)
left join file_upload_data fud on (f.id=fud.file_id)
where id > 108774660 and status='ok' and public='public' and banned=0 
and virusFound<2 and displayStatus not in ('illegal','maybe_illegal')
limit 10;
