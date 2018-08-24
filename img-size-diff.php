<?php


$images = [
'https://thumbs.uloz.to/M/T/u/xMTu9dNV.',
'https://thumbs.uloz.to/V/t/S/xVtSgYXM.',
'https://thumbs.uloz.to/a/C/u/xaCu9dNV.',
'https://thumbs.uloz.to/a/F/m/xaFm79h7.',
'https://thumbs.uloz.to/A/o/c/xAocaACN.',
'https://thumbs.uloz.to/C/1/H/xC1Hr46c.',
'https://thumbs.uloz.to/r/8/s/xr8sNDZJ.',
'https://thumbs.uloz.to/b/K/V/xbKV9b3u.',
'https://thumbs.uloz.to/J/r/L/xJrLWzrq.',
'https://thumbs.uloz.to/X/M/H/xXMHARQD.',
'https://thumbs.uloz.to/B/t/x/xBtx5TmC.',
'https://thumbs.uloz.to/j/z/V/xjzVvTmC.',
'https://thumbs.uloz.to/V/g/Z/xVgZuc8A.',
'https://thumbs.uloz.to/x/W/U/xxWUdRQD.',
'https://thumbs.uloz.to/M/k/6/xMk6hY8F.',
'https://thumbs.uloz.to/R/4/h/xR4hC3p1.',
'https://thumbs.uloz.to/g/t/J/xgtJs9d5.',
'https://thumbs.uloz.to/C/v/s/xCvsviUS.',
'https://thumbs.uloz.to/s/J/h/xsJhsTmC.',
'https://thumbs.uloz.to/a/r/z/xarz6YXM.',
'https://thumbs.uloz.to/R/V/w/xRVwgG73.',
'https://thumbs.uloz.to/R/Q/2/xRQ2viUS.',
'https://thumbs.uloz.to/9/P/p/x9PpdRQD.',
'https://thumbs.uloz.to/x/e/8/xxe8qb5a.',
'https://thumbs.uloz.to/c/2/S/xc2S3CyU.',
'https://thumbs.uloz.to/H/2/A/xH2Ac46c.',
'https://thumbs.uloz.to/p/P/M/xpPMpTZj.',
'https://thumbs.uloz.to/E/z/H/xEzHxUWo.',
'https://thumbs.uloz.to/2/K/A/x2KA6Mj9.',
'https://thumbs.uloz.to/N/G/6/xNG6J9ov.',
'https://thumbs.uloz.to/U/h/T/xUhT3vaT.'
];


$imagesPF = [
'https://thumbs.uloz.to/U/J/A/xUJA9GKw.',
'https://thumbs.uloz.to/R/W/t/xRWtrt87.',
'https://thumbs.uloz.to/X/v/W/xXvWSSBQ.',
'https://thumbs.uloz.to/9/n/x/x9nxzHUi.',
'https://thumbs.uloz.to/G/i/i/xGiiK6vo.',
'https://thumbs.uloz.to/V/W/9/xVW9ygW.',
'https://thumbs.uloz.to/d/x/5/xdx56iJA.',
'https://thumbs.uloz.to/K/P/o/xKPoDkkr.',
'https://thumbs.uloz.to/x/b/d/xxbdLha5.',
'https://thumbs.uloz.to/t/H/C/xtHCPTxX.',
'https://thumbs.uloz.to/9/z/h/x9zhedzx.',
'https://thumbs.uloz.to/N/a/w/xNawCt4y.',
'https://thumbs.uloz.to/E/4/s/xE4siXNz.',
'https://thumbs.uloz.to/A/N/d/xANdVVMT.',
'https://thumbs.uloz.to/T/V/W/xTVWqeiW.',
'https://thumbs.uloz.to/e/f/w/xefwZGA.',
'https://thumbs.uloz.to/q/o/A/xqoAttf.',
'https://thumbs.uloz.to/1/d/h/x1dh1BeW.',
'https://thumbs.uloz.to/H/m/P/xHmPuiGf.',
'https://thumbs.uloz.to/A/m/v/xAmvbva6.',
'https://thumbs.uloz.to/H/x/4/xHx4ReuT.',
'https://thumbs.uloz.to/2/H/p/x2Hpp5Nh.',
'https://thumbs.uloz.to/P/A/S/xPASGd8o.',
'https://thumbs.uloz.to/t/V/2/xtV2gRm3.',
'https://thumbs.uloz.to/c/v/r/xcvrTmYK.',
'https://thumbs.uloz.to/Z/W/Z/xZWZXoov.',
'https://thumbs.uloz.to/A/p/M/xApM7VdG.',
'https://thumbs.uloz.to/u/R/Q/xuRQnZjE.',
'https://thumbs.uloz.to/E/6/E/xE6EvkB7.',
'https://thumbs.uloz.to/c/y/o/xcyoSuNz.',
'https://thumbs.uloz.to/R/T/s/xRTsNYhu.'
];

mkdir('260x170');
mkdir('160x120');

foreach ($images as $img) {
	for ($i = 0; $i < 10; $i++) {
		$filename = $img . '260x170.' . $i . '.jpg';
		echo "$filename\n";
		file_put_contents('260x170/'.str_replace([':', '/'], ['', ''], $filename), file_get_contents($filename));
		$filename = $img . '160x120.' . $i . '.jpg';
		file_put_contents('160x120/'.str_replace([':', '/'], ['', ''], $filename), file_get_contents($filename));
	}
}

