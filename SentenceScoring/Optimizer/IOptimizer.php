<?php

namespace Tools\SentenceScoring\Optimizer;

use Tools\SentenceScoring\Dictionary\IWord;

/**
 * Interface IOptimizer.
 */
interface IOptimizer
{
	/**
	 * @return string
	 */
	public function getSentence();


	/**
	 * @param string $category
	 *
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getWordsByCategory($category);


	/**
	 * @param IWord $word
	 */
	public function removeWord(IWord $word);
}
