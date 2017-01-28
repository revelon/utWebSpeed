<?php

$cats = [];
$rows = explode("\n", file_get_contents('./v6.csv'));

//var_dump($rows);die;

foreach ($rows as $row) {
	$fields = explode(",", trim($row));
	$cats[$fields[1]][] = [$fields[0] => $fields[2]];
}


function showCat($c, $cats) {

	echo "\n\n\nxxxxx xxxxx xxxxx Showing Category: {$c} xxxxx xxxxx xxxxx\n\n\n";

	foreach ($cats[$c] as $k1 => $w1) {
		foreach ($cats[$c] as $k2 => $w2) {
			if (strpos(key($w2), key($w1)) !== FALSE && $k1 !== $k2) {
				$alert = (current($w1) == 1 || current($w2) == 1) ? "   !!! POZOR !!!" : "";
				echo "Ve vyrazu  " . key($w2) . ":". current($w2) . "  nalezen substring  " . key($w1) . ":" . current($w1) . $alert . "\n";
			}
		}
	}

}

showCat('L0', $cats);
showCat('L1', $cats);
showCat('L2', $cats);
showCat('L3', $cats);
showCat('L4', $cats);
showCat('L5', $cats);
