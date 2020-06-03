<?php

$slug = $_GET['slug'];
$part = $_GET['part'];
$sign = $_GET['sign'];
$chunkSize = $_GET['chunkSize']; // dummy for now, but we shoud/could do range requests instead of it


// try to resolve copies aka symlinls
$mapFile = './storage/symlinks.map';
$map = file_exists($mapFile) ? unserialize(file_get_contents($mapFile)) : [];
if (isset($map[$slug])) {
	$slug = $map[$slug];
	$skipSign = false; // necessary simplification hack, as there is original slug in sign included
} else {
	$skipSign = true;
}



if (!file_exists("./storage/${slug}.${part}") || !file_exists('./storage/'.$slug.'.meta')) {
	header('Content-Type: application/json');
	header('HTTP/1.1 404 Not Found');
	print json_encode(['status'=>'ko','reason'=>'File is not available']);
	exit();
}

$fileInfo = json_decode(file_get_contents('./storage/'.$slug.'.meta'));

if ($skipSign && ($fileInfo->sign !== $sign)) {
	header('HTTP/1.1 403 Forbidden');
	print json_encode(['status'=>'ko','reason'=>'Signature is incorrect']);
	exit();
}

// all ok, serve file
header('Content-Type: application/octet-stream');
readfile("./storage/${slug}.${part}");
