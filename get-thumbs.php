<?php

//http://videoth.uloz.to/e/D/U/xeDUb7NM.640x360.0.jpg

$ar = [
'xeDUb7NM', 'x6JMoPuS', 'xUAaDdpB', 'x9nDGztR', 'xJ139mZv', 'xuchF8pk', 'xkJpnzKe', 'x4gx1QtR', 'xXG2smUi', 'xHEemx7U', 'xNXK6vKe', 'xm3K1pi1', 'xoER4BjH', 'xPLnNUoy', 'xG9GzmBQ', 
'xwEK6XQp', 'xafP5YUK', 'xZp9sWW4', 'xqdvet3', 'xgNDtsFm', 'xpzVRyow', 'xVEgr4Di', 'xBosRsrR', 'xjd5azkh', 'xMpBPrY2', 'xsGAomTk', 'xKdzZChn', 'x45WxBeW', 'x621TLq', 'xMVxC89F'
];

foreach ($ar as $e) {
	mkdir("./{$e}/");
	for ($i = 0; $i < 10; $i++) {
		$name = $e . ".640x360.{$i}.jpg";
		file_put_contents("./{$e}/" . $name, file_get_contents("http://videoth.uloz.to/{$e[1]}/{$e[2]}/{$e[3]}/" . $name));
	}
}
