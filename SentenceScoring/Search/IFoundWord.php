<?php

namespace Tools\SentenceScoring\Search;

use Tools\SentenceScoring\Dictionary\IWord;

/**
 * Interface IFoundWord.
 */
interface IFoundWord
{
	/**
	 * @return IWord
	 */
	public function getWord();
}
