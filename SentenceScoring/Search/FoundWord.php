<?php

namespace Tools\SentenceScoring\Search;

use Tools\SentenceScoring\Dictionary\IWord;

/**
 * Class FoundWord.
 */
class FoundWord implements IFoundWord
{
	/**
	 * @var IWord
	 */
	protected $word;


	/**
	 * @param IWord $word
	 */
	public function __construct(IWord $word)
	{
		$this->word = $word;
	}


	/**
	 * @return IWord
	 */
	public function getWord()
	{
		return $this->word;
	}
}
