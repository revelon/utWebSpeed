<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '128M');

$jdsToGet = 9;
$howMany = 10;
$template = "jobs-banner.xsl"; // "jobs-banner.xsl" full HTML ... "jobs-banner-fragment.xsl" ... fragment only
$dataSource = "http://exporter.lmc.cz/jobs-all-uloz-to.xml"; // jobs-all-uloz-to.xml

echo "\n\nBeginning JD preparations...\n";

$xslDoc = new DOMDocument();
$xslDoc->load($template);

echo "Template {$template} loaded...\n";

$xmlDoc = new DOMDocument();
$xmlDoc->load($dataSource);

echo "Data loaded...\n";

$xPath = new DOMXPath($xmlDoc);
$jds = $xPath->query('/positionList/@count')->item(0)->nodeValue;

if (!$jds) {
	exit("Ending, no proper data on input...\n\n");
}

echo "Got document with {$jds} JDs...\n"; 

$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$proc->setParameter('', 'jdsToGet', $jdsToGet);

echo "XSLT processor instantiated...\n"; 

for ($i = 0; $i < $howMany; $i++) {
	generateJobsBanner(rand(0, ($jds-$jdsToGet)), $proc, $xmlDoc, $i);
}

echo "Ending generation...\n\n";




function generateJobsBanner($offset, $proc, $xmlDoc, $run) {

	echo "Using {$offset} offset...\n"; 

	$proc->setParameter('', 'offset', $offset);
	$output = $proc->transformToXML($xmlDoc);

	echo "Transformation {$run} done...\n";

	if ($output) {
		$file = "jobs-positions{$run}.html";

		echo "Saving to {$file} file...\n";

		file_put_contents($file, $output);
	} else {
		echo "Nothing to save...\n";
	}

}
