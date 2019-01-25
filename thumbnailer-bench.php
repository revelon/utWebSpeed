<?php

/*
Thumbnailer benchmark. Na vstupu ma nekolik adresaru s obrazky ktere se pokusi nejprve nahrat a pote opakovane stahnout, s ruznymi prefixy
Prvni download byva vyrazne pomalejsi nez ty ostatni... Je to vse jen seriove, synteticky, ale pro predstavu to docela dostacuje
*/

$dirs = [640,1024,1280,1440,1600,2000];  // dirnames in current directory with jpeg files
$run = 'grtx'; // random prefix
$server = "http://thumbs.test.infra.ci";  // thumbnailer instance

echo "Starting thumbs benchmark to server {$server} ...\n";

foreach ($dirs as $dir) {
	$files  = scandir('./'.$dir);

	echo "\nUploading files in directory {$dir}\n";
	$start = microtime(true);
	foreach ($files as $f) {
	    if ($f[0] === '.') continue;
	    $ff = strtr($f, "._", "xx");
	    $command = "curl -s -S {$server}:82/{$ff}{$run}{$dir} --data-binary @./{$f} -XPUT -H'Content-Type:image/jpeg' > /dev/null";
	    $a = shell_exec($command);
	    echo "*";
	    //var_dump($command, $a);
	}
	echo " Upload lasted: ". (microtime(true) - $start) ." s\n";

	echo "1st download of 260x170 quality from quality {$dir}\n";
	$start = microtime(true);
	foreach ($files as $f) {
	    if ($f[0] === '.') continue;
	    $ff = strtr($f, "._", "xx");
	    $r = file_get_contents("{$server}/{$f[0]}/{$f[1]}/{$f[2]}/{$ff}{$run}{$dir}.260x170.jpg");
	    echo (!sizeof($r)) ? " ERR " : "*";
	}
	echo " 1st download lasted ". (microtime(true) - $start) ." s\n";

	echo "2nd download of 260x170 quality from quality {$dir}\n";
	$start = microtime(true);
	foreach ($files as $f) {
	    if ($f[0] === '.') continue;
	    $ff = strtr($f, "._", "xx");
	    $r = file_get_contents("{$server}/{$f[0]}/{$f[1]}/{$f[2]}/{$ff}{$run}{$dir}.260x170.jpg");
	    echo (!sizeof($r)) ? " ERR " : "*";
	}
	echo " 2nd download lasted ". (microtime(true) - $start) ." s\n";

	echo "3rd download of 260x170 quality from quality {$dir}\n";
	$start = microtime(true);
	foreach ($files as $f) {
	    if ($f[0] === '.') continue;
	    $ff = strtr($f, "._", "xx");
	    $r = file_get_contents("{$server}/{$f[0]}/{$f[1]}/{$f[2]}/{$ff}{$run}{$dir}.260x170.jpg");
	    echo (!sizeof($r)) ? " ERR " : "*";
	}
	echo " 3rd download lasted ". (microtime(true) - $start) ." s\n";

}


