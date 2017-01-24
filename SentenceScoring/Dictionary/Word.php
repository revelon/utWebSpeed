<?php

namespace Tools\SentenceScoring\Dictionary;

use Tools\SentenceScoring\ISentenceScoringService;

/**
 * Class Word.
 */
class Word implements IWord
{
	/**
	 * @var string
	 */
	protected $word;

	/**
	 * @var string
	 */
	protected $category;

	/**
	 * @var string
	 */
	protected $matchType;


	/**
	 * Word constructor.
	 *
	 * @param string $word
	 * @param string $category
	 * @param string $matchType
	 */
	public function __construct($word, $category, $matchType)
	{
		$this->word = $word;
		$this->category = $category;
		$this->matchType = $matchType;
	}


	/**
	 * @return string
	 */
	public function getWord()
	{
		return $this->word;
	}


	/**
	 * @return string
	 */
	public function getCategory()
	{
		return $this->category;
	}


	/**
	 * @return string
	 */
	public function getMatchType()
	{
		return $this->matchType;
	}


	/**
	 * @param string $sentence
	 *
	 * @return bool
	 */
	public function isSubstringOf($sentence)
	{
		$word = $this->word;
		if ($this->matchType == ISentenceScoringService::MATCHTYPE_FULLMATCH) {
			$word = ' ' . $word . ' ';
		}

		return (bool)(strpos(' ' . $sentence . ' ', $word) !== false);
	}


	/**
	 * @param array $state
	 *
	 * @return Word @this
	 */
	public static function __set_state($state)
	{
		return new self($state['word'], $state['category'], $state['matchType']);
	}
}
