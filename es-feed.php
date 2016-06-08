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
limit 100000;

							 ")) {
	echo "Number of files: " . mysqli_num_rows($result);
    /* fetch associative array */

    $success = 0; $fail = 0;

    while ($row = $result->fetch_assoc()) {
        $command = "curl -s -XPUT \"http://localhost:9200/files/public/{$row['id']}\" -d'";
    	unset($row['id']);
        $command .= json_encode($row, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) . "'";
        echo "\n" . $command;
        $rawResponse = shell_exec($command);
        $response = json_decode($rawResponse);
        if ($response->created === true) {
            echo "\nSuccess";
            $success++;
        } else {
            //var_dump('failed', $response);
            echo "\nFail with: " . $rawResponse;
            $fail++;
        }
    }

    /* free result set */
    $result->close();
}





$mysqli->close();

echo "\n\nFinished with OKs: {$success}   and   KOs: {$fail}\n";


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

# klasicky relevatni search, dnesni strict
POST /files/_search
{
    "query": {
        "query_string": {
            "query": "petr rar",
            "default_operator": "and",
            "default_field": "keywords"
        }
    },
    "from" : 0, "size" : 10
}

# nebo asi lepsi, novejsi
POST /files/_search
{
    "query": {
        "query_string": {
            "query": "petr rar",
            "default_field": "keywords",
            "use_dis_max" : true
        }
    },
    "from" : 0, "size" : 10
}

# boostujeme obsah z neceskych geiop
POST /files/_search
{
    "query": {
        "bool": {
            "must": {
                "match": {
                    "keywords": { 
                        "query":    "petr rar",
                        "operator": "and"
                    }
                }
            },
            "should": [ 
                { "match": { "geoipcountry": "EG" }}
            ]
        }
    }
}



# skrze fuzziness muzeme mozna vyresit i preklepy zamerne preklepy v nazvech!!!


# sorting vypina relevanci !!
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

# agreguje podle hashid a sortuje podle cetnosti, size=0 nevraci zadny resultset krome agregaci
POST /files/_search
{
  "size": 0,
  "aggs": {
    "group_by_hashid": {
      "terms": {
        "field": "hashid"
      }
    }
  }
}

# pocet dokumentu s unikatnim hashid
POST /files/_search
{
  "size": 0,
  "aggs": {
    "hashid_count": {
      "cardinality": {
        "field": "hashid"
      }
    }
  }
}

# nejvetsi ci nejmensi hashid
POST /files/_search
{
  "size": 0,
  "aggs": {
    "hashid_min_max": {
      "max": {
        "field": "hashid"
      }
    }
  }
}


// boost limit strict search

// dodat i file_category!!! nejak

select count(*) from file f
left join file_hashflags fh using (hashid)
left join file_hash_multimedia fhm using (hashid)
left join file_upload_data fud on (f.id=fud.file_id)
where status='ok' and public='public' and banned=0 and size!=0
and virusFound<2 and displayStatus not in ('illegal','maybe_illegal');

+----------+
| count(*) |
+----------+
| 18832293 |
+----------+
1 row in set (11 min 30.66 sec)

select f.id, name, keywords_name as keywords, f.hashid, f.upload_date as uploaded, uploader_geoipcountry as geoipcountry, 
contentType, fh.size as sizeInKB, if(displayStatus in ('safe', 'maybe_safe'),'UT','PF') as realm, rating_status as rating, 
if(fhm.width>500 and fhm.height>500,'HQ','LQ') as quality, length as lengthInSec, 
if(streamable,1,0) as streamable, if(thumbVideo='',false,true) as liveFreeStreaming, 
if(archiveProtected>0 and password is not null,true,false) as archiveProtected, if(password,true,false) as passwordProtected 
from file f
left join file_hashflags fh using (hashid)
left join file_hash_multimedia fhm using (hashid)
left join file_upload_data fud on (f.id=fud.file_id)
where id > 108774660 and status='ok' and public='public' and banned=0 and size!=0 
and virusFound<2 and displayStatus not in ('illegal','maybe_illegal')
limit 10;
