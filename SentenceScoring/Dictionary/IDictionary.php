<?php

namespace Tools\SentenceScoring\Dictionary;

/**
 * Interface IDictionary.
 */
interface IDictionary
{
	/**
	 * @param string $category
	 *
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getWordsByCategory($category);


	/**
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getSingleWords();


	/**
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getWordBoundaries();


	/**
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getSubstringWords();
}
