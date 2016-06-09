<?php
// ElasticSearch data feeder for UT file data, experimental, by MR

// script is quite resource demanding, so reserve enough RAM for it
ini_set('memory_limit', '2048M');
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
$lastFileId = $lastFileId = (int) $result->fetch_assoc()['mx'];
// to be able to use sorting without too much overhead, we should narrow the window
$fedingOffset = 100000;
// limit of docs of one indexing batch call
$limit = 20000;

// ES index name, ES type name and ES indexer node bulk api address
$indexName = "files4";
$typeName = "public";
$esIndexerBulkUrl = "http://localhost:9200/_bulk"; // "http://es1.farm.int.nds:9200/_bulk"






// simple counters for succeded, refused sends and overall number of documents send to the search
$success = 0; $fail = 0; $totalDocs = 0;

// just timestamp to be able to measure indexing duration
$startAt = time();

// inerate until there is still something to do
while ($lastFileId) {

    echo "\nExecuting SQL for last file.id={$lastFileId}, elapsed time in seconds: " . (int)(time() - $startAt);

    if ($result = $mysqli->query("
        select f.id, name, keywords_name as keywords, f.hashid, f.upload_date as uploaded, uploader_geoipcountry as geoipcountry, 
        contentType, fh.size as sizeInKB, if(displayStatus in ('safe', 'maybe_safe'),'UT','PF') as realm, rating_status as rating, 
        if(fhm.width>=720 and fhm.height>=720,'HQ','LQ') as quality, length as lengthInSec, 
        if(streamable,1,0) as streamable, if(thumbVideo='',false,true) as liveFreeStreaming, 
        if(archiveProtected>0 and password is not null,true,false) as archiveProtected, if(password,true,false) as passwordProtected 
        from file f
        left join file_hashflags fh using (hashid)
        left join file_hash_multimedia fhm using (hashid)
        left join file_upload_data fud on (f.id=fud.file_id)
        where id between ". ($lastFileId-$fedingOffset) ." and {$lastFileId} and status='ok' and public='public' and banned=0 
        and flags2 not like '%searchable%' and virusFound<2 and displayStatus not in ('illegal','maybe_illegal') 
        order by id desc limit {$limit};
    							 ")) {

        $resultCount = mysqli_num_rows($result);
    	echo "\nNumber of files get: {$resultCount}, so far send OK {$totalDocs} documents...\n";

        // evaluation current iteration, whether and how to continue next, based on actual data
        if (!$resultCount && ($lastFileI <= $fedingOffset)) {
            echo ("\nNo more data to fetch, ending...");
            $lastFileId = 0;
            break;
        } elseif (!$resultCount) {
            $lastFileId -= $fedingOffset;
            continue;
        }

        // prepare command/payload of documents to be indexed; hint: we are assuming that named index&type already exists!
        $command = "";
        while ($row = $result->fetch_assoc()) {
        	$command .= '{ "index" : { "_index" : "' . $indexName . '", "_type" : "' . $typeName . '", "_id" : "' . $row['id'] . '" } }' . "\n";
            $lastFileId = $row['id'];
            unset($row['id']);
            $command .= json_encode($row, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) . "\n";
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

        // free current SQL statement
        $result->close();
        // and at least try to feebly free some memory
        gc_collect_cycles();
    }
}


// and we're almost done
$mysqli->close();

echo "\n\nFinished with OKs: {$success} and KOs: {$fail} of batches... in ".(time()-$startAt)." seconds \n";

exit(); // done

?>

Unimportant stuff and bluff follows...


DELETE /files4

GET /files4/_stats

PUT /files4
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

