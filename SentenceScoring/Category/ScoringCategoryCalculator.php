<?php

namespace Tools\SentenceScoring\Category;

use Tools\SentenceScoring\Dictionary\IDictionary;
use Tools\SentenceScoring\Optimizer\IOptimizer;

/**
 * Class ScoringCategoryCalculator.
 */
class ScoringCategoryCalculator extends AbstractScoringCalculator
{
	/**
	 * @var string
	 */
	protected $category;


	/**
	 * C0ScoringCalculator constructor.
	 *
	 * @param IOptimizer $optimizer
	 * @param IDictionary $dictionary
	 */
	public function __construct(IOptimizer $optimizer, IDictionary $dictionary, $category)
	{
		parent::__construct($optimizer, $dictionary);

		$this->category = $category;
	}


	/**
	 */
	protected function apply()
	{
		$foundWords = [];
		foreach ($this->optimizer->getWordsByCategory($this->category) as $word) {
			if ($word->isSubstringOf($this->optimizer->getSentence())) {
				$foundWords[] = $this->createFoundWord($word);
			}
		}
		$this->foundWords = $foundWords;
	}
}
