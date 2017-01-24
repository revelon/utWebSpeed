<?php

namespace Tools\SentenceScoring\Dictionary;

/**
 * Interface IWord.
 */
interface IWord
{
	/**
	 * @return string
	 */
	public function getWord();


	/**
	 * @return string
	 */
	public function getCategory();


	/**
	 * @return string
	 */
	public function getMatchType();


	/**
	 * @param string $sentence
	 *
	 * @return bool
	 */
	public function isSubstringOf($sentence);
}
