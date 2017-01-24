<?php

namespace Tools\SentenceScoring\Dictionary;

use Tools\SentenceScoring\ISentenceScoringService;

/**
 * Class AbstractDictionary.
 */
abstract class AbstractDictionary implements IDictionary
{
	/**
	 * @var array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	protected $singleWords = [];

	/**
	 * @var array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	protected $wordsByCategory = [];

	/**
	 * @var array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	protected $wordBoundaries = [];

	/**
	 * @var array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	protected $substringWords = [];


	/**
	 * @param \Tools\SentenceScoring\Dictionary\IWord $word
	 */
	protected function addWordToSets(IWord $word)
	{
		$this->wordsByCategory[$word->getCategory()][$word->getWord()] = $word;

		$parts = explode(' ', $word->getWord());
		$lastPart = null;
		foreach ($parts as $part) {
			// single slova
			$this->singleWords[$part][] = $word;

			// hranice slov
			if (!is_null($lastPart)) {
				// posledne pismeno slova + prve pismeno dalsieho
				$this->wordBoundaries[substr($lastPart, -1) . ' ' . substr($part, 0, 1)][] = $word;
			}
			$lastPart = $part;
		}

		// substringy sa nedaju matchovat len cez '==' porovnanie dvoch slov, takze sa im musime venovat extra
		// v pripade ze to je jednoslovne, nedokazeme to matchnut ani cez hranice slov
		// zostava nam len hladat cez substring
		if ($word->getMatchType() == ISentenceScoringService::MATCHTYPE_SUBSTRING
			&& count($parts) == 1
		) {
			$this->substringWords[] = $word;
		}
	}


	/**
	 * @param string $category
	 *
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getWordsByCategory($category = null)
	{
		if (is_null($category)) {
			return $this->wordsByCategory;
		}

		return $this->wordsByCategory[$category];
	}


	/**
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getWordBoundaries()
	{
		return $this->wordBoundaries;
	}


	/**
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getSingleWords()
	{
		return $this->singleWords;
	}


	/**
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getSubstringWords()
	{
		return $this->substringWords;
	}
}
