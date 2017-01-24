<?php

namespace Tools\SentenceScoring\Dictionary;


use Nodus\IOException;
use Tools\SentenceScoring\ScoringService;

/**
 * Dictionary Factory
 */
class DictionaryFactory implements IDictionaryFactory
{

	/**
	 * @var IDictionary
	 */
	protected $dictionary;

	/**
	 * @var IDictionaryPaths
	 */
	protected $dictionaryPaths;

	/**
	 * @var boolean Use the pre-generated PHP class?
	 */
	protected $useGeneratedFile;


	/**
	 * DictionaryFactory constructor.
	 *
	 * @param IDictionaryPaths $dictionaryPaths
	 * @param bool $useGeneratedFile
	 */
	public function __construct(IDictionaryPaths $dictionaryPaths, $useGeneratedFile)
	{
		$this->dictionaryPaths = $dictionaryPaths;
		$this->useGeneratedFile = $useGeneratedFile;
	}


	/**
	 * @return IDictionary
	 */
	public function getDictionary()
	{
		if ($this->dictionary === null) {
			$this->dictionary = $this->useGeneratedFile ? $this->loadCsvDictionary() : $this->createCsvDictionary();
		}

		return $this->dictionary;
	}


	/**
	 * Returns the differences between two versions of a dictionary.
	 *
	 * @param int $version Version to compare.
	 * @param int $refVersion Reference version.
	 *
	 * @return array
	 */
	public function getChangedWords($version, $refVersion = ScoringService::VERSION)
	{
		$file = @file($this->dictionaryPaths->getCsvFilePath($version));
		if (!is_array($file) || count($file) == 0) {
			throw new \InvalidArgumentException('Invalid CSV file for version ' . $version);
		}

		$refFile = @file($this->dictionaryPaths->getCsvFilePath($refVersion));
		if (!is_array($refFile) || count($refFile) == 0) {
			throw new \InvalidArgumentException('Invalid CSV file for version ' . $refVersion);
		}

		$rawDiff = array_merge(array_diff($file, $refFile), array_diff($refFile, $file));
		$result = [];
		foreach ($rawDiff as $row) {
			$result[] = explode(',', $row, 2)[0];
		}

		return array_unique($result);
	}


	/**
	 * Creates a fresh dictionary from CSV file.
	 *
	 * @return CsvDictionary
	 */
	protected function createCsvDictionary()
	{
		$csvFilePath = $this->dictionaryPaths->getCsvFilePath(ScoringService::VERSION);

		return new CsvDictionary($csvFilePath);
	}


	/**
	 * Load the pre-generated PHP class
	 *
	 * @return CsvDictionary
	 */
	protected function loadCsvDictionary()
	{
		$generatedFilePath = $this->dictionaryPaths->getGeneratedFilePath();
		$dictionary = include($generatedFilePath);

		if ($dictionary === false) {
			throw new \Exception("Include SentenceScoring dictionary from '$generatedFilePath' failed. What about run 'php Libs/SentenceScoring/Tools/createDictionaryFile.php'?");
		}

		return $dictionary;
	}
}
