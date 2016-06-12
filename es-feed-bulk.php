<?php
// ElasticSearch data feeder for UT file data, experimental, by MR

// script is quite resource demanding, so reserve enough RAM for it, just for the case...
ini_set('memory_limit', '2018M');
// read necessary DB credentials
require ('/Users/xrevelon/cnf.php');

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

if ($mysqli->connect_errno) {
    printf("Connection failed: %s\n", $mysqli->connect_error);
    exit();
}

// query to get highest file_id from the db
$result = $mysqli->query("select max(id) mx from file");





// we are indexing from most recent (most important) to eldest documents
$lastFileId = (int) $result->fetch_assoc()['mx'];
//$lastFileId = 67053067; // override for cases of unexpected crash... elder than 14.2M are much much slower to feed... also 39698776
// to be able to use sorting without too much overhead, we should narrow the window
$feedingOffset = 90000;
// limit of docs of one indexing batch call
$limit = 20000;

// ES index name, ES type name and ES indexer node bulk api address
$indexName = "files5";
$typeName = "public";
//$esIndexerBulkUrl = "http://localhost:9200/_bulk";
$esIndexerBulkUrl = "http://es1.farm.int.nds:9200/_bulk";






// simple counters for succeded, refused sends and overall number of documents send to the search
$success = 0; $fail = 0; $totalDocs = 0;

// just timestamp to be able to measure indexing duration
$startAt = time();

// inerate until there is still something to do
while ($lastFileId > 0) {

    echo "\nExecuting SQL for last file.id={$lastFileId}, elapsed time in seconds: " . (int)(time() - $startAt);

    if ($result = $mysqli->query("
        select f.id, name, keywords_name as keywords, f.hashid, f.upload_date as uploaded, uploader_geoipcountry as geoipcountry, 
        contentType, fh.size as sizeInKB, if(displayStatus in ('safe', 'maybe_safe'),'UT','PF') as realm, rating_status as rating, 
        if(fhm.width>=720 and fhm.height>=720,'HQ','LQ') as quality, length as lengthInSec, if(pornHumanCheck=2,1,0) as gayPorn, 
        if(streamable,1,0) as streamable, if(thumbVideo='' and flags2 like '%videoshare%',false,true) as liveFreeStreaming, 
        if(archiveProtected>0 and password is not null,true,false) as archiveProtected, if(password,true,false) as passwordProtected 
        from file f
        left join file_hashflags fh using (hashid)
        left join file_hash_multimedia fhm using (hashid)
        left join file_upload_data fud on (f.id=fud.file_id)
        where id between ". ($lastFileId-$feedingOffset) ." and {$lastFileId} and status='ok' and public='public' and banned=0 
        and flags2 not like '%searchable%' and virusFound<2 and displayStatus not in ('illegal','maybe_illegal') 
        order by id desc limit {$limit};
    							 ")) {

        $resultCount = mysqli_num_rows($result);
    	echo "\nNumber of files get:  {$resultCount},  so far send OK {$totalDocs} documents, with memory usage in MB: " 
            . round(memory_get_usage(true)/1048576,2) . "\n";

        // evaluation current iteration, whether and how to continue next, based on actual data
        if (!$resultCount && ($lastFileId <= $feedingOffset)) {
            echo ("\nNo more data to fetch, ending...");
            $lastFileId = 0;
            break;
        } elseif ($resultCount < 2) {
            $lastFileId -= $feedingOffset;
            echo ("\nNot enough data in set, shifting lastFileId to " . $lastFileId);
            continue;
        }

        // prepare command/payload of documents to be indexed; hint: we are assuming that named index&type already exists!
        $command = "";
        while ($row = $result->fetch_assoc()) {
        	$command .= '{ "index" : { "_index" : "' . $indexName . '", "_type" : "' . $typeName . '", "_id" : "' . $row['id'] . '" } }' . "\n";
            $lastFileId = $row['id'];
            $command .= json_encode(cleanRecord($row), JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) . "\n";
            //$command .= json_encode(cleanRecord($row), JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) . "\n";
        }

        // create temp file with payload to send and if we succeed, delete it, otherwise left for later re-send/analysis
        $temp = tempnam('.', 'feed');
        file_put_contents($temp, $command);

        // execute the send command itself and evaluate its result
        $rawResponse = shell_exec('curl -s -XPOST "' . $esIndexerBulkUrl . '" --data-binary "@'.$temp.'"');
        $response = json_decode($rawResponse);
        if ($response->errors === false) {
            echo "\nSuccessful send";
            $success++;
            $totalDocs += $resultCount;
            // tidy up the mess in current directory
            unlink($temp);
        } else {
            echo "\nFailed send on {$temp} file with: " . $rawResponse;
            $fail++;
            // do not unlink file to keep it for potential re-send or analysis
        }

        // free current SQL statement and at least try to feebly free some memory...
        $result->close();
        unset($command);
        unset($result);
        unset($resultCount);
        unset($row);
        unset($temp);
        unset($rawResponse);

        gc_collect_cycles();
    } else {
        echo "DB Query failed...";
    }
}


// and we're almost done
$mysqli->close();

echo "\n\nFinished with OKs: {$success} and KOs: {$fail} of batches... in ".(time()-$startAt)." seconds \n";

exit(); // done

// helper function cleaning out all unnecessary items and reduce index size
function cleanRecord($row) {
    unset($row['id']);
    if ($row['geoipcountry'] === null) unset($row['geoipcountry']);
    if ($row['contentType'] === '') unset($row['contentType']);
    if (!in_array($row['contentType'], array('video', 'audio', 'image'))) unset($row['quality']);
    if (!in_array($row['contentType'], array('video', 'audio'))) unset($row['lengthInSec']);
    // predelat vsechno na tagy????
    if (!$row['streamable']) unset($row['streamable']);
    if (!$row['liveFreeStreaming']) unset($row['liveFreeStreaming']);
    if (!$row['archiveProtected']) unset($row['archiveProtected']);
    if (!$row['passwordProtected']) unset($row['passwordProtected']);
    if (!$row['gayPorn']) unset($row['gayPorn']);
    return $row;
}

?>

Unimportant stuff and bluff follows...


DELETE /files5

GET /files5/_stats

PUT /files5
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
        },
        "gayPorn": {
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



POST /files3/_search
{
  "query": {
    "match": {
      "keywords": "MASH 243"
    }
  },
  "aggs": {
    "my-top-docs": {
      "terms": {
        "field": "hashid",
        "order": {
          "top_hit": "desc"
        }
      },
      "aggs": {
        "top_tags_hits": {
          "top_hits": {}
        },
        "top_hit" : {
          "max": {
            "script": "_score"
          }
        }
      }
    }
  }
}


POST /files3/_search
{
    "query": {
        "match": {
            "keywords": "MASH 243"
        }
    },
    "size": 3,
    "aggs": {
        "top-tags": {
            "terms": {
                "field": "hashid",
                "size": 10
            },
            "aggs": {
                "top_tag_hits": {
                    "top_hits": {
                        "sort": [
                            {
                                "uploaded": {
                                    "order": "desc"
                                }
                            }
                        ],
                        "_source": {
                            "include": [
                                "name", "hashid", "id"
                            ]
                        },
                        "size" : 1
                    }
                }
            }
        }
    }
}

last id indexed so far...
39455592

# ziskani nejvyssiho ID dokumentu
GET /files4/_search
{
  "fields": [
    "_id"
  ],
  "query": {
    "match_all": {}
  },
  "sort": {
    "_id": "desc"
  },
  "size": 1
}

# pokus o negativni boosting, ne prilis uspesny, ty do + jdou mnohem lepe!!
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
                { "match": {
                    "geoipcountry": {
                        "query": "EG",
                        "boost": 0.000000001 
                    }
                }}
            ]
        }
    }
}

# todo: range or grater than

# nemame last-modified, takze zrychlit mazani muzeme mozna tim, ze vsechny "DEL" si budme psat do vedlejsi tabulky a promazavat podle ni a nebo ten sloupec pridat
# multi_match by se mohl hodit po zavedeni tagu
# pridat gaye a vykopat nepotrebne fieldy???

# range request pro videa mezi 5m a 1h
POST /files4/_search
{
    "query" : {
        "constant_score" : {
            "filter" : {
                "range" : {
                    "lengthInSec" : {
                        "gte" : 300,
                        "lte"  : 3600
                    }
                }
            }
        }
    }
}
