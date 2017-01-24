<?php

namespace Tools\SentenceScoring\Search;

/**
 * Interface ISearch.
 */
interface ISearch
{
	/**
	 * @return string
	 */
	public function getOriginalSentence();


	/**
	 * @return string
	 */
	public function getPreparedSentence();


	/**
	 * @param string $category
	 *
	 * @return IFoundWord[]
	 */
	public function findWordsInCategory($category);


	/**
	 * @return IFoundWord[]
	 */
	public function findWords();
}
