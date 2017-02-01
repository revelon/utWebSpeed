<?php

//echo "\nInput 1: " . $argv[1] . ' , Input 2: ' . $argv[2] . "\n";


$in = explode("\n", trim(file_get_contents($argv[1])));
foreach ($in as $row) {
	$line = explode(";", $row);
	if (strlen($line[1]) > 10) {
		echo "porn\t" . strtolower($line[1]) . "\n";
	}
}

