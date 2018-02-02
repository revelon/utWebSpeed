<?php

# misc report tool

require ('/Users/xrevelon/git/ulozto-web/Nodus/Security/IntEncrypt.php');
require ('/Users/xrevelon/cnf.php');

echo "\n start at: " . date(DATE_RFC2822) . "\n\n";

$mysqli = new mysqli($cnf['h'], $cnf['u'], $cnf['p'], $cnf['d']);

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$reports = [
    "SELECT date(NOW()) datum, 'velikost a pocet vsech OK hashu' report, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file_hashflags WHERE cdnStatus='ok'",
    "SELECT date(NOW()) datum, 'pocet vsech OK souboru' report, COUNT(*) pocet, SUM(IF(public='public',1,0)) verejnych, SUM(IF(flags2 LIKE '%searchable%'),1,0) nevyhledatelnych FROM file WHERE status='ok'",
    "SELECT date(NOW()) datum, 'velikost a pocet vsech OK kopii' report, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file_origins fo LEFT JOIN file f ON (f.id=fo.file_id) LEFT JOIN file_hashflags USING (hashid) WHERE f.status='ok'",
    "SELECT date(NOW()) datum, 'velikost a pocet vsech OK hashu dle contentType' report, contentType, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags WHERE cdnStatus='ok' GROUP BY contentType",
    "SELECT date(NOW()) datum, 'velikost a pocet vsech OK hashu dle displayStatus' report, displayStatus, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file_hashflags WHERE cdnStatus='ok' GROUP BY displayStatus",
    "SELECT date(NOW()) datum, 'velikost a pocet OK souboru dle name_status' report, name_status, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file LEFT JOIN file_hashflags USING (hashid) WHERE f.status='ok' GROUP BY name_status",
    "SELECT date(NOW()) datum, 'velikost a pocet OK souboru i hashu virusFound>1' report, COUNT(*) pocet_souboru, COUNT(DISTINCT f.hashid) pocet_hashu, ROUND(SUM(size)/1000/1000/1000) GB_souboru FROM file LEFT JOIN file_hashflags USING (hashid) WHERE f.status='ok' AND virusFound>1",
    "SELECT date(NOW()) datum, 'velikost a pocet OK hashu podle banu' report, IF(banned=2,'hardban','softban') typ, COUNT(*), ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags WHERE cdnStatus='ok' AND banned>0 GROUP BY typ",
    "SELECT date(NOW()) datum, 'velikost a pocet OK hashu interniho streamingu delsich 10 minut dle realmu' report, displayStatus, COUNT(*), ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file_hash_multimedia fhm JOIN file_hashflags USING (hashid) WHERE streamable=1 AND cdnStatus='ok' AND length>600 GROUP BY displayStatus",
    "SELECT date(NOW()) datum, 'velikost a pocet OK hashu OK externiho streamingu delsich 10 minut dle realmu' report, displayStatus, COUNT(*), ROUND(SUM(size)/1000/1000/1000/1000) TB FROM external_stream_file JOIN file_hashflags ON (file_hashid=hashid) WHERE cdnStatus='ok' AND status='ok' AND length>600 GROUP BY displayStatus",
    "SELECT date(NOW()) datum, 'velikost a pocet OK hashu bez jakychkoliv OK souboru' report, COUNT(*), ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file_hashflags fh LEFT JOIN file f ON (f.hashid=fh.hashid AND f.status='ok') WHERE cdnStatus='ok' AND banned<2 AND f.id IS NULL",
    "SELECT date(NOW()) datum, 'velikost a pocet OK hashu s nejakymi OK souboru' report, COUNT(*), ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file_hashflags fh JOIN file f ON (f.hashid=fh.hashid AND f.status='ok') WHERE cdnStatus='ok' AND banned<2",
    "SELECT date(NOW()) datum, 'velikost a pocet OK video, archive ci image souboru s nahledy' report, contentType, COUNT(*) pocet, IF((contentType='image' AND thumbImage='') OR (contentType IN ('video','archive') AND thumbSlideshow=''),0,1) s_nahledy, ROUND(SUM(size)/1000/1000/1000/1000) TB FROM file f JOIN file_flags ff ON (f.id=ff.file_id) LEFT JOIN file_hashflags USING (hashid) WHERE cdnStatus='ok' AND f.status='ok' AND contentType IN ('video','archive','image') GROUP BY contentType",

];




foreach ($reports as $statement) {
    if ($result = $mysqli->query($statement)) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $k => $v) echo "$k : $v \t";
            echo "\n";
            flush();
        }
        $result->close();
    }
}
$mysqli->close();

echo "\n end at: " . date(DATE_RFC2822) . "\n\n";
