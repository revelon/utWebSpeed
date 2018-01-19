<?php

/**
 * Humble external Skrivy's CDN demo page, client side processing only, without any callbacks
 */
ini_set('memory_limit', '1012M');

// some basic configurations

// ulozto account with a lot of credit
$httpBasicCredentials = 'https://srw-test:srwtest123@';
// api authorization token, editable on CDN admin GUI
$authToken = 'd45068a3c2f948356b88222182b83807';
// project id
$project = 1;
//$authToken = 'fsvda345676i5rhe23456t7866uy4567t67itu34ye534y5r56rye4y';
$authToken = 'd45068a3c2f948356b88222182b83807';
//$project = 3;
// api base CDN URL path
$apiBase = 'https://service.ulozto.srw.cz/api/v1';
// shared secret, shoudl exists nowhere but in source configuration on both sides
define('STORAGE_SECRET', 'Krakonos666');

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

/**
 * Returns string with HTML markup for page output
 *
 * @param $name debug name
 * @param $jsonObj json object to pretty print
 * @param $command optional command by which was json obtained
 */
function debugHelper ($name, $jsonObj, $command = null) {
	if ($command) {
		$command .= "\n\n";
	}
	return "<br><details><summary>Debug: {$name}</summary><pre>{$command}" . 
		json_encode($jsonObj, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . 
		"</pre></details>";
}


// dirty mixed templating and PHP follows, sorry for this mess, but it was very quick to do...
?>

<!doctype html>
<html lang="cs">
	<head>
		<meta charset="utf-8">
		<title>External streaming demo</title>

		<style>
			body {font-family: sans-serif;}
			h2 {margin-top: 40px; border-top: 2px solid maroon}
			label {margin-right: 30px; background-color: beige; pading: 5px;}
			textarea {width: 80%; height: 80px;}
			video {width: 40%;}
			summary {cursor: pointer; font-weight: bold; font-style: oblique;}
			pre {background-color: lightgray; font-size: 9px; font-family: monospace;}
		</style>

	</head>
	<body>

	<details>
	  <summary>General request debug:</summary>
	  <pre>
<?php print_r($_REQUEST); ?>
	  </pre>
	</details>











	<!-- step 1, basic inputs, files URLs to send and convert on external CDN-->
	<h2>Step 1: get files and request conversion</h2>
	<form method="post">
		<textarea name="filesToConvert" placeholder="In format of common file detail, each line one file"
></textarea>
		<input type="submit">
	</form>

<?php

	$requestIds = [];
	foreach (explode("\n", $_REQUEST['filesToConvert']) as $key => $val) {
		if (!trim($val)) continue;
		$url = str_replace("https://", $httpBasicCredentials, trim($val));
		$command = "curl -X POST -d '{\"url\":\"{$url}\",\"project\":{$project}}' -H \"Content-Type: application/json\" -H \"X-Auth-Token: {$authToken}\" {$apiBase}/fetch";
		$ret = shell_exec($command);
		$response = json_decode($ret);
		$requestIds[] = $response->id;

		echo debugHelper("Request No. {$key} got id={$response->id}", $response, $command);
	}









// render step 2, conditionally, waiting for processing of requested files to finish on remote CDN
if (count($requestIds) || $_REQUEST['requestIdsTocheck']) {
	$requestIdsToCheck = $requestIds ?: explode("\n", $_REQUEST['requestIdsTocheck']);
?>

	<h2>Step 2: check requests for conversion status (repeat it as long as you get status fail or done)</h2>
	<form method="get">
		<textarea name="requestIdsTocheck" placeholder="In format of common request ids, each line one file">
<?php echo implode("\n", $requestIdsToCheck); ?> 
		</textarea>
		<input type="submit">
	</form>

<?php

	$filesCreated = [];
	foreach ($requestIdsToCheck as $key => $val) {
		if (!trim($val)) continue;
		$command = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase}/fetch/" . trim($val);
		$ret = shell_exec($command);
		$response = json_decode($ret);
		if ($response->status === 'done') {
			$filesCreated[] = $response->file;
		}
		echo debugHelper("Request No. {$key} got status={$response->status}", $response, $command);
	}

}









// render step 3, conditionally, once we have atl least some files converted and ready, we can start playing with them
if (count($filesCreated) || $_REQUEST['filesList']) {
	$filesList = $filesCreated ?: explode("\n", $_REQUEST['filesList']);
	$operation = $_REQUEST['operation'];
?>

	<h2>Step 3: manipulate processed (done) files, with either check, play or delete / wait for conversion</h2>
	<form method="post">
		<textarea name="filesList" placeholder="In format of common file ids, each line one file">
<?php echo implode("\n", $filesList); ?> 
		</textarea><br>
		<label>check<input type="radio" name="operation" value="check" 
<?php echo ($operation == "check" || !$operation) ? "checked" : ""; ?>
			></label> 
		<label>play<input type="radio" name="operation" value="play"
<?php echo ($operation == "play") ? "checked" : ""; ?>
			></label>
		<label>delete<input type="radio" name="operation" value="delete"
<?php echo ($operation == "delete") ? "checked" : ""; ?>
			></label>
		<input type="submit">
	</form>

	<h4>Performing operation: 
<?php echo $operation; ?>
	</h5>

<?php

	$filesManipulation = [];
	$md5s = [];
	foreach ($filesList as $key => $val) {
		if (!trim($val)) continue;
		// check
		switch ($operation) {
			case 'check':
				$command = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase}/files/" . trim($val);
				$ret = shell_exec($command);
				$response = json_decode($ret);
				break;
			case 'delete':
				$command = "curl -X DELETE -H \"X-Auth-Token: {$authToken}\" {$apiBase}/files/" . trim($val);
				$ret = shell_exec($command);
				$response = json_decode($ret);
				break;
			case 'play':
				$command = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase}/files/" . trim($val);
				$ret = shell_exec($command);
				$response = json_decode($ret);

				if (!$response->hasIssue && $response->related->conversion) {
					$playUrl = $response->related->conversion;
					if (is_object($playUrl)) { // decide available quality
						$playUrl = (string) $response->related->conversion->{'720'} ?: (string) $response->related->conversion->{'480'};
					} else if (is_array($playUrl)) {
						$playUrl = (string) $playUrl[0];
					}
					$subtitles = (array) $response->subtitles;

					list($server, $path) = explode('/', $playUrl, 2);

					$videoPlayableUrl = 'http://' . $playUrl . '?' . http_build_query(getParams('/' . $path));

					// build player markup, perhaps prepare lang/iso code mapping...
					$video = "<br><details><summary>Video id={$val} named: {$response->filename}</summary><video crossorigin controls src='{$videoPlayableUrl}'>";
					foreach ($subtitles as $lang => $subUrl) {
						list($server, $path) = explode('/', $subUrl, 2);
						$subPlayableUrl = 'http://' . $subUrl . '?' . http_build_query(getParams('/' . $path));
						$video .= "<track srclang='{$lang}' label='{$lang}' kind='subtitles' src='{$subPlayableUrl}' default>";
					}
					// preview hacked in
					$videoPreviewUrl = 'http://' . str_replace('/view/', '/preview/', $playUrl) . '?' . http_build_query(getParams('/pre' . $path, 120));
					$video .= "</video><h5>Preview</h5><video crossorigin controls src='{$videoPreviewUrl}'>";
					foreach ($subtitles as $lang => $subUrl) {
						list($server, $path) = explode('/', $subUrl, 2);
						$subPlayableUrl = 'http://' . $subUrl . '?' . http_build_query(getParams('/' . $path, 120));
						$video .= "<track srclang='{$lang}' label='{$lang}' kind='subtitles' src='{$subPlayableUrl}' default>";
					}
					$video .= "</video></details>";
					echo $video;
				}
				break;
		}
		if (!$response->hasIssue && $response->related->conversion) $md5s[trim($val)] = $response->md5;
		//if (!$response->hasIssue) $md5s[] = $response->md5;
		$hasIssue =	((!$response && $operation !== 'delete') || $response->hasIssue || !$response->size) ? "YES" : "NO";
		echo debugHelper("Request No. {$key} has issue? " . $hasIssue . " ... and  is conversion ready? " . 
			(($hasIssue !== "YES" && $response->related->conversion) ? "YES" : "NOT YET..."), $response, $command);
	}
	echo "MD5s available: '", implode("','", $md5s), "'\n<br>\n Key/MD5 pairs<pre>";
	foreach ($md5s as $key => $value) {
		echo $key . ',' . $value . "\n";
	}
	echo "</pre>";
}







?>

<!-- dummy file-on-storage explorer, returns IDs, for occasional use only, HEAD calls should be faster, but are they broken? -->
	<br><br><hr><hr><hr><br><br>
	<h3>File IDs available on streaming server (very stupid and slow explorer / helper)</h3>
	<form method="get">
		<label><input type="checkbox" name="exploreFiles" value="1"> Yes, please explore the files for me</label>
		<input type="hidden" name="filesList" value="1">
		<input type="hidden" name="operation" value="check">
		<input type="submit">
	</form>

<?php
if ($_REQUEST['exploreFiles']) {
	$fileIdsAvailable = $debugs = "";
	for ($i = 1; $i < 40; $i++) {
		echo "."; ob_flush();flush();
		$command = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase}/files/" . $i;
		$ret = shell_exec($command);
		$response = json_decode($ret);
		if ($ret && $response && $response->id) {
			$fileIdsAvailable .= $i . "\n";
			$debugs .= debugHelper("File No. {$i}", $response, $command);
		}
	}
?>

	<h5>Explored file IDs:</h5>
	<textarea name="existingFileIds" placeholder="In format of common file ids, each line one file">
<?php echo $fileIdsAvailable; ?> 
	</textarea><br>

<?php echo $debugs;

}


