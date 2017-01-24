<?php

namespace Tools\SentenceScoring\Dictionary;

use Tools\SentenceScoring\ScoringService;

/**
 * Class DictionaryFactory.
 */
class DictionaryFileGenerator
{
	/**
	 * @var IDictionaryPaths
	 */
	protected $dictionaryPaths;


	/**
	 * DictionaryFileGenerator constructor.
	 * @param IDictionaryPaths $dictionaryPaths
	 */
	public function __construct(IDictionaryPaths $dictionaryPaths)
	{
		$this->dictionaryPaths = $dictionaryPaths;
	}


	/**
	 * Vytvori soubor, z ktereho se nacita Dictionary v metode getDictionary
	 * @return void
	 */
	public function createDictionaryFile()
	{
		$csvPath = $this->dictionaryPaths->getCsvFilePath(ScoringService::VERSION);
		$dictionary =  new CsvDictionary($csvPath);

		$code = [];
		$code[] = "<?php\r\n";
		$code[] = "return unserialize('" . serialize($dictionary) . "');\r\n";

		file_put_contents($this->dictionaryPaths->getGeneratedFilePath(), $code);
	}
}
