<?php

ini_set('display_errors', 1);

@include 'link-generator/src/ILinkGenerator.php';
@include 'link-generator/src/LinkGeneratorException.php';
@include 'link-generator/src/LinkGenerator.php';
@include '../../link-generator/src/ILinkGenerator.php';
@include '../../link-generator/src/LinkGeneratorException.php';
@include '../../link-generator/src/LinkGenerator.php';

use Videohostingcz\LinkGenerator;

// some basic configurations

// ulozto account with a lot of credit
$httpBasicCredentials = 'https://srw-test:srwtest123@';
// api authorization token, editable on CDN admin GUI
$authToken = 'd45068a3c2f948356b88222182b83807';
// project id
$project = 1;
// api base CDN URL path
$apiBase = 'https://service.ulozto.srw.cz/api/v1';
$apiBase2 = 'https://service.ulozto.srw.cz/api/v2';
// shared secret, should exists nowhere but in source configuration on both sides
define('STORAGE_SECRET', 'Krakonos666');
define('STORAGE_SECRET2', 'xeeb6eJ5cah7OoviaoPhi1eieHocha7v');

/**
 * Returns array with query params, shared signing algorithm for both sides
 *
 * @param $path Path part of the url starting with / (e.g. /view/1234.mp4)
 * @param $expires Timestamp when the link expires (default: time() + 3600)
 * @param $ip optional current IP address of the user
 */
function getParams($path, $expires = NULL, $ip = NULL) {
  $expires = ($expires == NULL) ? (time() + 3600) : (time() + $expires);

  $hash = $expires . $path . STORAGE_SECRET . ($ip !== NULL ? $ip : '');
  $hash = md5($hash, TRUE);
  $hash = base64_encode($hash);
  $hash = str_replace([ '+', '/', '=' ], [ '-', '_', '' ], $hash);

  $result = [
    'expires' => $expires,
    'hash' => $hash,
  ];
  if ($ip) $result['ip'] = $ip;
  return $result;
}

$val = 11;
$out = [];

// $command = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase}/files/" . trim($val);
// $ret = shell_exec($command);
// $response = json_decode($ret);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$apiBase}/files/" . trim($val));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Auth-Token: ${authToken}"]);
$server_output = curl_exec ($ch);
//var_dump('<pre>', $server_output, curl_getinfo($ch, CURLINFO_HEADER_OUT));
curl_close ($ch);
$response = json_decode($server_output);

if (!$response->hasIssue && $response->related->conversion) {
	$playUrl = $response->related->conversion;
	if (is_object($playUrl)) { // decide available quality
		$playUrl = (string) $response->related->conversion->{'720'} ?: (string) $response->related->conversion->{'480'};
	} else if (is_array($playUrl)) {
		$playUrl = (string) $playUrl[0];
	}
	$subtitles = (array) $response->subtitles;

	$out['fileName'] = $response->filename;
	$out['duration'] = (int) $response->detail->duration;

	list($server, $path) = explode('/', $playUrl, 2);

	$videoPlayableUrl = 'https://' . $playUrl . '?' . http_build_query(getParams('/' . $path));
	$out['playUrl'] = $videoPlayableUrl;

	foreach ($subtitles as $lang => $subUrl) {
		list($server, $path) = explode('/', $subUrl, 2);
		$subPlayableUrl = 'https://' . $subUrl . '?' . http_build_query(getParams('/' . $path));
		$out['sub'][$lang] = $subPlayableUrl;
	}

	//$command2 = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase2}/files/" . trim($val);
	//$ret2 = shell_exec($command2);
	//$response2 = json_decode($ret2);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "{$apiBase2}/files/" . trim($val));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Auth-Token: ${authToken}"]);
	$server_output2 = curl_exec ($ch);
	curl_close ($ch);
	$response2 = json_decode($server_output2);

	$sizelimit = floor($response2->video->conversion[0]->size/12);
	$generator = new LinkGenerator();
	$urlp = $generator->generate($response2->video->conversion[0]->uri, 
		[	'limitsize' => $sizelimit, 
			'limitid' => uniqid(), 
			'rate' => '400k', 
			'expires' => time(null) + 30, 
			'sparams' => 'path'], 
			STORAGE_SECRET2);
	$out['previewUrl'] = $urlp;
}

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode($out);

