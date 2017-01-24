<?php

namespace Tools\SentenceScoring\Optimizer;

use Tools\SentenceScoring\Dictionary\IDictionary;
use Tools\SentenceScoring\Dictionary\IWord;

/**
 * Class Optimizer.
 */
class Optimizer implements IOptimizer
{
	/**
	 * @var string
	 */
	protected $sentence;

	/**
	 * @var array|string[]
	 */
	protected $sentenceBoundaries;

	/**
	 * @var array|string[]
	 */
	protected $sentenceSingleWords;

	/**
	 * @var IDictionary
	 */
	protected $dictionary;

	/**
	 * @var array
	 */
	protected $wordsByCategory = [];


	/**
	 * Optimizer constructor.
	 *
	 * @param string $sentence
	 * @param IDictionary $dictionary
	 */
	public function __construct($sentence, IDictionary $dictionary)
	{
		$this->sentence = $sentence;
		$this->dictionary = $dictionary;

		$this->prepareSentenceParts($sentence);

		$this->prepareIndices();
	}


	/**
	 * @param string $sentence
	 */
	protected function prepareSentenceParts($sentence)
	{
		$this->sentenceSingleWords = [];
		$this->sentenceBoundaries = [];

		$parts = explode(' ', $sentence);
		$lastPart = null;
		foreach ($parts as $part) {
			// single words to match
			$this->sentenceSingleWords[] = $part;

			// word boundaries to match
			if (!is_null($lastPart)) {
				// posledne pismeno slova + prve pismeno dalsieho
				$this->sentenceBoundaries[] = substr($lastPart, -1) . ' ' . substr($part, 0, 1);
			}
			$lastPart = $part;
		}
	}


	/**
	 * Pripravi zoznam slov, ktore je potencialne mozne aplikovat na nasu vetu $sentence.
	 */
	protected function prepareIndices()
	{
		$this->wordsByCategory = [];

		// single slova
		$singleWords = $this->dictionary->getSingleWords();
		foreach ($this->sentenceSingleWords as $sentenceSingleWord) {
			if (isset($singleWords[$sentenceSingleWord])) {
				/** @var IWord[] $words */
				$words = $singleWords[$sentenceSingleWord];
				foreach ($words as $word) {
					$this->wordsByCategory[$word->getCategory()][$word->getWord()] = $word;
				}
			}
		}

		// hranice slov
		$boundaries = $this->dictionary->getWordBoundaries();
		foreach ($this->sentenceBoundaries as $sentenceBoundary) {
			if (isset($boundaries[$sentenceBoundary])) {
				/** @var IWord[] $words */
				$words = $boundaries[$sentenceBoundary];
				foreach ($words as $word) {
					$this->wordsByCategory[$word->getCategory()][$word->getWord()] = $word;
				}
			}
		}

		// substringy
		$substringWords = $this->dictionary->getSubstringWords();
		foreach ($substringWords as $word) {
			if ($word->isSubstringOf($this->sentence) >= 0) {
				$this->wordsByCategory[$word->getCategory()][$word->getWord()] = $word;
			}
		}
	}


	/**
	 * @return string
	 */
	public function getSentence()
	{
		return $this->sentence;
	}


	/**
	 * @param string $category
	 *
	 * @return array|\Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getWordsByCategory($category)
	{
		return (isset($this->wordsByCategory[$category])) ? $this->wordsByCategory[$category] : [];
	}


	/**
	 * @param IWord $word
	 */
	public function removeWord(IWord $word)
	{
		$w = $word->getWord();
		$this->sentence = preg_replace(
			'/(^[^ ]*| [^ ]*)' . preg_quote($w, '/') . '([^ ]* |[^ ]*$)/',
			'$1' . str_repeat('*', mb_strlen($w)) . '$2',
			$this->sentence
		);
	}
}
