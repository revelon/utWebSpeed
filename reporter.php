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
    "SELECT DATE(NOW()) datum, 'velikost a pocet vsech OK hashu' report, COUNT(*) pocet, banned, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags WHERE cdnStatus='ok' GROUP BY banned",
    "SELECT DATE(NOW()) datum, 'pocet vsech OK souboru' report, COUNT(*) pocet, SUM(IF(public='public',1,0)) verejnych, SUM(IF(flags2 LIKE '%searchable%',1,0)) nevyhledatelnych FROM file WHERE status='ok'",
    "SELECT DATE(NOW()) datum, 'velikost a pocet vsech OK kopii ktere si uzivatele vyrobili sami ve FM nebo kopii do oblibenych' report, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file_origins fo LEFT JOIN file f ON (f.id=fo.file_id) LEFT JOIN file_hashflags USING (hashid) WHERE f.status='ok'",
    "SELECT DATE(NOW()) datum, 'velikost a pocet vsech OK hashu dle contentType' report, contentType, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags WHERE cdnStatus='ok' GROUP BY contentType",
    "SELECT DATE(NOW()) datum, 'velikost a pocet vsech OK hashu dle displayStatus' report, displayStatus, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags WHERE cdnStatus='ok' GROUP BY displayStatus",
    "SELECT DATE(NOW()) datum, 'velikost a pocet OK souboru dle name_status' report, name_status, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file LEFT JOIN file_hashflags USING (hashid) WHERE status='ok' GROUP BY name_status",
    "SELECT DATE(NOW()) datum, 'velikost a pocet OK souboru i hashu virusFound>1' report, COUNT(*) pocet_souboru, COUNT(DISTINCT f.hashid) pocet_hashu, ROUND(SUM(size)/1000/1000/1000) GB_souboru FROM file f LEFT JOIN file_hashflags USING (hashid) WHERE f.status='ok' AND virusFound>1",
    "SELECT DATE(NOW()) datum, 'velikost a pocet OK hashu podle banu' report, IF(banned=2,'hardban','softban') typ, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags WHERE cdnStatus='ok' AND banned>0 GROUP BY typ",
    "SELECT DATE(NOW()) datum, 'velikost a pocet OK hashu interniho streamingu delsich 10 minut dle realmu' report, displayStatus, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hash_multimedia fhm JOIN file_hashflags USING (hashid) WHERE streamable=1 AND cdnStatus='ok' AND length>600 GROUP BY displayStatus",
    "SELECT DATE(NOW()) datum, 'velikost a pocet OK hashu OK externiho streamingu delsich 10 minut dle realmu' report, displayStatus, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM external_stream_file JOIN file_hashflags ON (file_hashid=hashid) WHERE cdnStatus='ok' AND status='ok' AND length>600 GROUP BY displayStatus",
    "SELECT DATE(NOW()) datum, 'velikost a pocet OK nezabanovanych hashu bez jakychkoliv OK souboru' report, COUNT(*) pocet, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags fh LEFT JOIN file f ON (f.hashid=fh.hashid AND f.status='ok') WHERE cdnStatus='ok' AND banned<2 AND f.id IS NULL",
    "SELECT DATE(NOW()) datum, 'pocet OK nezabanovanych hashu s nejakymi OK soubory' report, COUNT(DISTINCT fh.hashid) pocet FROM file_hashflags fh INNER JOIN file f ON (f.hashid=fh.hashid AND f.status='ok') WHERE cdnStatus='ok' AND banned<2",

    "SELECT DATE(NOW()) datum, 'velikost a pocet OK video a archiv souboru s nahledy' report, contentType, COUNT(*) pocet, SUM(IF(thumbSlideshow='',0,1)) s_nahledy, ROUND(SUM(size)/1000/1000/1000) GB FROM file f JOIN file_flags ff ON (f.id=ff.file_id) LEFT JOIN file_hashflags USING (hashid) WHERE cdnStatus='ok' AND f.status='ok' AND contentType IN ('video','archive') GROUP BY contentType",
    "SELECT DATE(NOW()) datum, 'velikost a pocet OK image souboru s nahledy' report, COUNT(*) pocet, SUM(IF(thumbImage='' OR thumbImage IS NULL,0,1)) s_nahledy, ROUND(SUM(size)/1000/1000/1000) GB FROM file f LEFT JOIN file_hashflags USING (hashid) WHERE cdnStatus='ok' AND f.status='ok' AND contentType='image'",

    "SELECT DATE(NOW()) datum, 'pocet a status uzivatelskych presunu mezi realmy' report, WEEKOFYEAR(created) tyden, COUNT(*) pocet, status, SUM(IF(fr.from_realm_id=1,1,0)) na_PF, SUM(IF(fr.from_realm_id=2,1,0)) na_UT FROM file_realm_transfer_request fr WHERE created>'2018-01-01 00:00:08' GROUP BY tyden, status ORDER BY tyden",

    "SELECT DATE(NOW()) datum, 'pocet, velikost a typ utraceni kreditu po tydnech' report, WEEKOFYEAR(datum) tyden, COUNT(*) pocet, type, ROUND(SUM(kredit_kb)/1000/1000) GB FROM uzivatele_kreditminus uk WHERE uk.datum>'2018-01-01 00:00:00' GROUP BY tyden, type ORDER BY tyden",
    "SELECT DATE(NOW()) datum, 'pocet, velikost a contentType utraceneho kreditu po tydnech bez obnov z kose' report, WEEKOFYEAR(datum) tyden, COUNT(*) pocet, contentType, ROUND(SUM(kredit_kb)/1000/1000) GB FROM uzivatele_kreditminus uk LEFT JOIN file f ON (uk.file_id=f.id) LEFT JOIN file_hashflags fh USING (hashid) WHERE uk.datum>'2018-01-01 00:00:00' AND type!='trash_restore' GROUP BY tyden, contentType ORDER BY tyden",
    "SELECT DATE(NOW()) datum, 'pocet, velikost a displayStatus utraceneho kreditu po tydnech bez obnov z kose' report, WEEKOFYEAR(datum) tyden, COUNT(*) pocet, displayStatus, ROUND(SUM(kredit_kb)/1000/1000) GB FROM uzivatele_kreditminus uk LEFT JOIN file f ON (uk.file_id=f.id) LEFT JOIN file_hashflags fh USING (hashid) WHERE uk.datum>'2018-01-01 00:00:00' AND type!='trash_restore' GROUP BY tyden, displayStatus ORDER BY tyden",

    "SELECT DATE(NOW()) datum, 'pocet vsech souboru a pocet stazeni a shlednuti na nich, vsechny statusy' report, COUNT(*) pocet_souboru, SUM(IF(views=0 AND downloads=0,1,0)) soubory_bez_stazeni_ci_shlednuti, SUM(downloads) pocet_stazeni, SUM(views) pocet_shlednuti FROM file",
    "SELECT DATE(NOW()) datum, 'pocet shlednuti LIVE a NON-LIVE video souboru, vsechny statusy, dle displayStatus' report, displayStatus, SUM(IF(thumbVideo!='' AND length<=600,1,0)) pocet_live_souboru, SUM(IF(thumbVideo!='' AND length<=600,views,0)) pocet_live_shlednuti, SUM(IF(thumbVideo!='' OR length<=600,0,1)) pocet_non_live_souboru, SUM(IF(thumbVideo!='' OR length<=600,0,views)) pocet_non_live_shlednuti FROM file LEFT JOIN file_hashflags fh USING (hashid) WHERE contentType='video' GROUP BY displayStatus",

    "SELECT DATE(NOW()) datum, 'pocet vsech nahranych souboru po tydnech' report, WEEKOFYEAR(upload_date) tyden, COUNT(*) pocet FROM file WHERE upload_date>'2018-01-01 00:00:00' GROUP BY tyden",
    "SELECT DATE(NOW()) datum, 'pocet a velikost nahranych jen novych hashu po tydnech, dle contentType' report, WEEKOFYEAR(created) tyden, COUNT(*) pocet, contentType, ROUND(SUM(size)/1000/1000/1000) GB FROM file_hashflags WHERE created>'2018-01-01 00:00:00' GROUP BY tyden, contentType ORDER BY tyden",

    "SELECT DATE(NOW()) datum, 'pocet vsech novych registraci po tydnech' report, WEEKOFYEAR(registrace) tyden, COUNT(*) pocet FROM uzivatele u WHERE registrace>'2018-01-01 00:00:00' GROUP BY tyden",
    "SELECT DATE(NOW()) datum, 'pocet vsech novych placenych registraci po tydnech' report, WEEKOFYEAR(paid) tyden, COUNT(*) pocet FROM uzivatele u LEFT JOIN user_credit uc ON (u.id=uc.uzivatele_id AND WEEKOFYEAR(registrace)=WEEKOFYEAR(paid) AND YEAR(registrace)=YEAR(paid) AND TYPE='payment') WHERE registrace>'2018-01-01 00:00:00' AND paid>'2018-01-01 00:00:00' GROUP BY tyden",
    
    "SELECT DATE(NOW()) datum, 'vsech novych SMS registraci po tydnech' report, WEEKOFYEAR(uc.paid) tyden, COUNT(*) pocet  FROM uzivatele u LEFT JOIN user_credit uc ON (u.id=uc.uzivatele_id AND (uc.paid BETWEEN (u.registrace - INTERVAL 1 SECOND) AND (u.registrace + INTERVAL 1 SECOND))) LEFT JOIN pricelist p ON (uc.pricelist_id = p.id) WHERE uc.status='paid' AND u.registrace>'2018-01-01 00:00:00' AND uc.paid>'2018-01-01 00:00:00' AND uc.type='payment' AND sms_keyword IS NOT NULL GROUP BY tyden",
    "SELECT DATE(NOW()) datum, 'pocet notifikaci o prihlaseni z noveho zarizeni celkem' report, COUNT(*) pocet_emailu, COUNT(DISTINCT user_id) pocet_ruznych_uzivatelu FROM user_login_device WHERE notified_at>'2017-12-20 17:40:00'",
    "SELECT DATE(NOW()) datum, 'pocet notifikaci o prihlaseni z noveho zarizeni po tydnech' report, WEEKOFYEAR(notified_at) tyden, COUNT(*) pocet_emailu, COUNT(DISTINCT user_id) ruznym_uzivatelum FROM user_login_device WHERE notified_at>'2018-01-01 00:00:00' GROUP BY tyden",

];



foreach ($reports as $statement) {
    if ($result = $mysqli->query($statement)) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $k => $v) echo "$k : $v \t";
            echo "\t" . date(DATE_RFC2822) . "\n";
            flush();
        }
        $result->close();
    }
}
$mysqli->close();

echo "\n end at: " . date(DATE_RFC2822) . "\n\n";
