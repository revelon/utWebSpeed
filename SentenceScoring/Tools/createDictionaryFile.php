<?php

require __DIR__ . '/../../../vendor/autoload.php';

$cacheDir = sys_get_temp_dir() . '/SentenceScoring.RobotLoaderCache_' . rand(1000, 9999);

// Init
if (is_dir($cacheDir)) {
	echo "Directory '$cacheDir' already exists.";
	exit(1);
}

mkdir($cacheDir);

$robotLoader = new Nette\Loaders\RobotLoader();
$robotLoader->addDirectory(__DIR__ . '/../');
$robotLoader->setCacheStorage(new Nette\Caching\Storages\FileStorage($cacheDir));
$robotLoader->register();

// Vygenerujeme
$dictionaryPaths = new \Tools\SentenceScoring\Dictionary\DictionaryPaths();
$generator = new \Tools\SentenceScoring\Dictionary\DictionaryFileGenerator($dictionaryPaths);
$generator->createDictionaryFile();

// Zkontrolujeme
$generatedFilepath = $dictionaryPaths->getGeneratedFilePath();
$dictionary = require($generatedFilepath);
if ($dictionary === false) {
	echo "Including file '$generatedFilepath' failed.";
	exit(1);
}

// Uklidime
exec("rm -rf '$cacheDir'");
