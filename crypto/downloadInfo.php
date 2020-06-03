<?php

$slug = $_GET['slug'];
$sign = $_GET['sign'];


// try to resolve copies aka symlinls
$mapFile = './storage/symlinks.map';
$map = file_exists($mapFile) ? unserialize(file_get_contents($mapFile)) : [];
if (isset($map[$slug])) {
	$slug = $map[$slug];
	$skipSign = false; // necessary simplification hack, as there is original slug in sign included
} else {
	$skipSign = true;
}


$directory = './storage/';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));

$parts = array_filter($scanned_directory, function($file) {
	global $slug;
	return (strpos($file, $slug) !== false) ? true : false;
});



header('Content-Type: application/json');

if ((sizeOf($parts) < 2) || !file_exists('./storage/'.$slug.'.meta')) {
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

print json_encode(['status'=>'ok','slug'=>$slug, 'chunks'=>(sizeOf($parts)-1), 'chunkSize'=>$fileInfo->chunkSize, 
	'fileName'=>$fileInfo->fileName, 'fileSize'=>$fileInfo->fileSize, 'mime'=>$fileInfo->mime]);

