<?php

namespace Tools\SentenceScoring\Dictionary;

class DictionaryPaths implements IDictionaryPaths
{
	/**
	 * @const string
	 */
	const CSV_FILE_PATH = '../Data/porncheckData.%d.csv';

	/**
	 * @const string
	 */
	const GENERATED_FILE_PATH = '../Data/generated/CachedDictionary.php';


	/**
	 * @param int $version
	 *
	 * @return string
	 */
	public function getCsvFilePath($version)
	{
		return __DIR__ . DIRECTORY_SEPARATOR . sprintf(static::CSV_FILE_PATH, $version);
	}

	/**
	 * @return string
	 */
	public function getGeneratedFilePath()
	{
		return __DIR__ . DIRECTORY_SEPARATOR . static::GENERATED_FILE_PATH;
	}
}
