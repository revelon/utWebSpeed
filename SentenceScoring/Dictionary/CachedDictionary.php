<?php

namespace Tools\SentenceScoring\Dictionary;

/**
 * Class CachedDictionary.
 */
class CachedDictionary extends AbstractDictionary
{
	/**
	 * CachedDictionary constructor.
	 *
	 * @param array $words
	 * @param array $wordsByCategory
	 */
	public function __construct(array $words = [], array $wordsByCategory = [])
	{
		$this->words = $words;
		$this->wordsByCategory = $wordsByCategory;
	}
}
