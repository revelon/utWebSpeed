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
select distinct hashid, lower(hex(hash)) hsh from file_hashflags left join file_hash using (hashid) left join file using (hashid) 
where contentType='image' and pornProbability=1 and status='ok' and public='public' and displayStatus='maybe_safe' 
and cdnStatus='ok' and hashid < 54502470
order by hashid desc limit 5000
							 ")) {
	// first hashID checked: 59024028
	echo "Number of files: " . mysqli_num_rows($result) . 
		"<style>img {display:inline-block;width:244px;max-height:244px;border:1px dashed gray;}</style><hr>";
    /* fetch associative array */
    $lastid = 0;
    while ($row = $result->fetch_assoc()) {
    	//var_dump($row);
        echo "<a href=http://imageth.uloz.to/{$row['hsh'][0]}/{$row['hsh'][1]}/{$row['hsh'][2]}/{$row['hsh']}.640x360.jpg 
                 download={$row['hsh']}.640x360.jpg title=hashid-{$row['hashid']}>
        <img src=http://imageth.uloz.to/{$row['hsh'][0]}/{$row['hsh'][1]}/{$row['hsh'][2]}/{$row['hsh']}.640x360.jpg></a>";
        $lastid = $row['hashid'];
    }

    /* free result set */
    $result->close();
}





$mysqli->close();

echo "\n\nFinished, last hashid={$lastid}\n";


