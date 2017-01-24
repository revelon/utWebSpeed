<?php

namespace Tools\SentenceScoring\Dictionary;

use Tools\SentenceScoring\ISentenceScoringService;
use Tools\SentenceScoring\ScoringException;

/**
 * Class AbstractDictionary.
 */
class CsvDictionary extends AbstractDictionary
{
	/**
	 * @const int
	 */
	const CSV_COLUMN_INDEX_WORD = 0;

	/**
	 * @const int
	 */
	const CSV_COLUMN_INDEX_CATEGORY = 1;

	/**
	 * @const int
	 */
	const CSV_COLUMN_INDEX_MATCHTYPE = 2;

	/**
	 * @var array
	 */
	protected $categoryTranslateTable = [
		'L0' => ISentenceScoringService::CATEGORY_0,
		'L1' => ISentenceScoringService::CATEGORY_1,
		'L2' => ISentenceScoringService::CATEGORY_2,
		'L3' => ISentenceScoringService::CATEGORY_3,
		'L4' => ISentenceScoringService::CATEGORY_4,
		'L5' => ISentenceScoringService::CATEGORY_5,
	];


	/**
	 * CsvDictionary constructor.
	 *
	 * @param string $csvFilePath
	 */
	public function __construct($csvFilePath)
	{
		$this->loadData($csvFilePath);
	}


	/**
	 * @param string $csvFilePath
	 *
	 * @throws ScoringException
	 */
	protected function loadData($csvFilePath)
	{
		if ($vocabFile = fopen($csvFilePath, 'r')) {
			while ($line = fgetcsv($vocabFile)) {
				$lineCount = count($line);
				if ($lineCount !== 3) {
					throw new ScoringException(
						'Incorrect csv input file. Wrong column count in line: '
						. json_encode($line)
						. ' from file: ' . $csvFilePath
					);
				}

				$word = new Word(
					$line[self::CSV_COLUMN_INDEX_WORD],
					$this->translateCategory($line[self::CSV_COLUMN_INDEX_CATEGORY]),
					(int)$line[self::CSV_COLUMN_INDEX_MATCHTYPE]
				);

				$this->addWordToSets($word);
			}
			fclose($vocabFile);
		}
	}


	/**
	 * @param string $csvCategory
	 *
	 * @return string
	 */
	protected function translateCategory($csvCategory)
	{
		return $this->categoryTranslateTable[$csvCategory];
	}
}
