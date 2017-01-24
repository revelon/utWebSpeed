<?php

$httpBasicCredentials = 'https://srw-test:srwtest123@';
$authToken = 'd45068a3c2f948356b88222182b83807';
$apiBase = 'https://service.ulozto.srw.cz/api/v1';



var_dump('General input debug:', $_REQUEST);


?>

<!doctype html>
<html lang="cs">
	<head>
		<meta charset="utf-8">
		<title>External streaming demo</title>

		<style>
			textarea {width:90%; height:150px;}
		</style>

		<script>
		</script>
	</head>
	<body>



	<!-- step 1 -->
	<h4>Step 1: get files and request conversion</h4>
	<form method="get">
		<textarea name="filesToConvert" placeholder="In format of common file detail, each line one file">
			https://uloz.to/!HCxth9Q3hDrj/v9-caso-magister-negi-magi-dvdrip-xvid-mp3-gb-big5-26-mkv
			https://uloz.to/!qZT0su2TtAYQ/v9-caso-magister-negi-magi-dvdrip-xvid-mp3-gb-big5-19-mkv
		</textarea>
		<input type="submit">
	</form>

	<hr>

<?php

	$requestIds = [];
	foreach (explode("\n", $_REQUEST['filesToConvert']) as $val) {
		if (!trim($val)) continue;
		$url = str_replace("https://", $httpBasicCredentials, trim($val));
		$command = "curl -X POST -d '{\"url\":\"{$url}\",\"project\":1}' -H \"Content-Type: application/json\" -H \"X-Auth-Token: {$authToken}\" {$apiBase}/fetch";
		$ret = shell_exec($command);
		$requestIds[] = json_decode($ret)->id;
		var_dump($ret, '<br>');
	}


// render step 2, conditionally
if (count($requestIds) || $_REQUEST['requestIdsTocheck']) {
	$requestIdsToCheck = $requestIds ? $requestIds : explode("\n", $_REQUEST['requestIdsTocheck'])
?>

	<h4>Step 2: check requests for conversion status (repeat as long as you get status fail or done)</h4>
	<form method="get">
		<textarea name="requestIdsTocheck">
<?php echo implode("\n", $requestIdsToCheck); ?> 
		</textarea>
		<input type="submit">
	</form>

	<hr>

<?php

	$filesCreated = [];
	foreach ($requestIdsToCheck as $val) {
		if (!trim($val)) continue;
		$command = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase}/fetch/" . trim($val);
		$ret = shell_exec($command);
		$response = json_decode($ret);
		if ($response->status === 'done') {
			$filesCreated[] = $response->file;
		}
		var_dump($response, '<br>');
	}

}


// render step 3, conditionally
if (count($filesCreated) || $_REQUEST['filesList']) {
	$filesList = $filesCreated ? $filesCreated : explode("\n", $_REQUEST['filesList'])
?>

	<h4>Step 3: manipulate inidvidual files, either delete, check or play</h4>
	<form method="get">
		<textarea name="filesList">
<?php echo implode("\n", $filesList); ?> 
		</textarea><br>
		<label>check<input type="radio" name="operation" value="check" checked></label>
		<label>play<input type="radio" name="operation" value="play"></label>
		<label>delete<input type="radio" name="operation" value="delete"></label>
		<input type="submit">
	</form>

	<hr>

<?php

	$filesManipulation = [];
	foreach ($filesList as $val) {
		if (!trim($val)) continue;
		// check
		$command = "curl -H \"X-Auth-Token: {$authToken}\" {$apiBase}/files/" . trim($val);
		$ret = shell_exec($command);
		$response = json_decode($ret);
		/*if ($response->status === 'done') {
			$filesCreated[] = $response->file;
		}*/
		var_dump($response, '<hr>');
	}

}
