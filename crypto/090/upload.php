<?php

$slug = $_REQUEST['slug'];
$part = $_REQUEST['part'];
file_put_contents("./storage/${slug}.${part}", file_get_contents('php://input')); // save file chunk itself
file_put_contents("./storage/${slug}.meta", json_encode(['chunkSize'=>$_REQUEST['chunkSize'], 'sign'=>$_REQUEST['sign'],
	'fileName'=>$_REQUEST['fileName'], 'size'=>$_REQUEST['size'], 'mime'=>$_REQUEST['mime']])); // save file metadata
