<?php

namespace Tools\SentenceScoring\Category;

use Tools\SentenceScoring\Dictionary\IDictionary;
use Tools\SentenceScoring\Dictionary\IWord;
use Tools\SentenceScoring\Optimizer\IOptimizer;
use Tools\SentenceScoring\Search\FoundWord;

/**
 * Class C0ScoringCalculator.
 */
abstract class AbstractScoringCalculator implements IScoringCategoryCalculator
{
	/**
	 * @var IOptimizer
	 */
	protected $optimizer;

	/**
	 * @var IDictionary
	 */
	protected $dictionary;

	/**
	 * @var \Tools\SentenceScoring\Dictionary\IWord[]
	 */
	protected $foundWords = null;


	/**
	 * C0ScoringCalculator constructor.
	 *
	 * @param string $sentence
	 * @param IOptimizer $optimizer
	 * @param IDictionary $dictionary
	 */
	public function __construct(IOptimizer $optimizer, IDictionary $dictionary)
	{
		$this->optimizer = $optimizer;
		$this->dictionary = $dictionary;
	}


	/**
	 * Workhorse method. Must set $this->foundWords.
	 */
	abstract protected function apply();


	/**
	 * @return int
	 */
	public function getResult()
	{
		return count($this->getFoundWords());
	}


	/**
	 * @return \Tools\SentenceScoring\Dictionary\IWord[]
	 */
	public function getFoundWords()
	{
		if (is_null($this->foundWords)) {
			$this->apply();
		}

		return $this->foundWords;
	}


	/**
	 * @param IWord $word
	 *
	 * @return FoundWord
	 */
	protected function createFoundWord(IWord $word)
	{
		return new FoundWord($word);
	}
}
