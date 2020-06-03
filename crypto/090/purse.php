<?php

// https://javascript.info/fetch-crossorigin
header("Access-Control-Allow-Methods: GET,PUT,POST,PATCH,DELETE");
header("Access-Control-Expose-Headers: PurseDigest");
header('Content-Type: application/json');


// for pre-flight mode only
if ($_REQUEST['id'] && $_REQUEST['action']=='get') {

	if (!file_exists("./purse/".$_REQUEST['id'].".meta") || !file_exists("./purse/".$_REQUEST['id'])) {	
		header('HTTP/1.1 404 Not Found');
		print json_encode(['status'=>'ko','reason'=>'Keystore is not available']);
		exit();
	}
}


if ($_REQUEST['action']=='put' && $_REQUEST['id'] && $_REQUEST['digest'] && $_REQUEST['initialDigest'] && $_REQUEST['sign']) {

	$meta = null;
	if (file_exists("./purse/".$_REQUEST['id'].".meta") && file_exists("./purse/".$_REQUEST['id'])) {
		$meta = json_decode(file_get_contents("./purse/".$_REQUEST['id'].".meta"));
	}
	if ($meta && ($meta->digest !== $_REQUEST['initialDigest'])) {
		header('Content-Type: application/json');
		header('HTTP/1.1 403 Forbidden');
		print json_encode(['status'=>'ko','reason'=>'Original file has been changed in between, reload, update and try again']);
	} elseif ($meta && $meta->sign !== $_REQUEST['sign']) {
		header('HTTP/1.1 403 Forbidden');
		print json_encode(['status'=>'ko','reason'=>'Signature is incorrect']);
	} else {
		file_put_contents("./purse/".$_REQUEST['id'], file_get_contents('php://input'));
		file_put_contents("./purse/".$_REQUEST['id'].".meta", json_encode(['digest'=>$_REQUEST['digest'], 'sign'=>$_REQUEST['sign']]));
		print json_encode(['status'=>'ok']);
	}

} elseif ($_REQUEST['action']=='get' && $_REQUEST['id'] && $_REQUEST['sign']) {

	$meta = null;
	if (file_exists("./purse/".$_REQUEST['id'].".meta")) {
		$meta = json_decode(file_get_contents("./purse/".$_REQUEST['id'].".meta"));
	}

	if ($meta->sign !== $_REQUEST['sign']) {
		header('HTTP/1.1 403 Forbidden');
		print json_encode(['status'=>'ko','reason'=>'Signature is incorrect']);
	} else {
		header('PurseDigest: '.$meta->digest);
		header('Content-Type: application/octet-stream');
		print file_get_contents("./purse/".$_REQUEST['id']);
	}

} else {
	header('HTTP/1.1 404 Not Found');
	print json_encode(['status'=>'ko','reason'=>'Some error happened']);
}
