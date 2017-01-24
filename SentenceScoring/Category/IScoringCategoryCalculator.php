<?php

namespace Tools\SentenceScoring\Category;

use Tools\SentenceScoring\Search\IFoundWord;

/**
 * Interface IScoringCategoryCalculator.
 */
interface IScoringCategoryCalculator
{
	/**
	 * Vraci pocet nalezenych vyskytu.
	 *
	 * @return int
	 */
	public function getResult();


	/**
	 * @return IFoundWord[]
	 */
	public function getFoundWords();
}
