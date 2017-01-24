<?php

namespace Tools\SentenceScoring\Search;

use Tools\SentenceScoring\Bridge\IPrepareKeywords;
use Tools\SentenceScoring\Category\IScoringCategoryCalculator;
use Tools\SentenceScoring\Category\ScoringCategoryCalculator;
use Tools\SentenceScoring\Category\ScoringCategoryCleanupCalculator;
use Tools\SentenceScoring\Dictionary\IDictionary;
use Tools\SentenceScoring\ISentenceScoringService;
use Tools\SentenceScoring\Optimizer\Optimizer;

/**
 * Class Search.
 */
class Search implements ISearch
{
	/**
	 * @var string[] poradi, v kterem se vyhledava v kategoriich
	 */
	protected static $categoriesOrder = [
		ISentenceScoringService::CATEGORY_0,    // nejdriv jdou goodwordy
		ISentenceScoringService::CATEGORY_5,    // potom badwordy
		ISentenceScoringService::CATEGORY_4,
		ISentenceScoringService::CATEGORY_3,    // trojka odmaze nektera slova a ostatni je uz nepouzivaji
		ISentenceScoringService::CATEGORY_1,
		ISentenceScoringService::CATEGORY_2,
	];

	/**
	 * @var string
	 */
	protected $originalSentence;

	/**
	 * @var string
	 */
	protected $preparedSentence;

	/**
	 * @var IDictionary
	 */
	protected $dictionary;

	/**
	 * @var IPrepareKeywords
	 */
	protected $keywordPrepare;

	/**
	 * @var IFoundWord[]
	 */
	protected $foundWords = [];

	/**
	 * @var IScoringCategoryCalculator[]
	 */
	protected $calculators;


	/**
	 * @param IDictionary $dictionary
	 * @param IPrepareKeywords $keywordPrepare
	 * @param string $sentence
	 */
	public function __construct(IDictionary $dictionary, IPrepareKeywords $keywordPrepare, $sentence)
	{
		$this->dictionary = $dictionary;
		$this->keywordPrepare = $keywordPrepare;
		$this->originalSentence = (string)$sentence;
	}


	/**
	 * @return string
	 */
	public function getOriginalSentence()
	{
		return $this->originalSentence;
	}


	/**
	 * @return string
	 */
	public function getPreparedSentence()
	{
		if ($this->preparedSentence === null) {
			$this->preparedSentence = $this->keywordPrepare->stripToKeywords($this->originalSentence);
		}

		return $this->preparedSentence;
	}


	/**
	 * @param string $category
	 *
	 * @return IFoundWord[]
	 *
	 * @throws \InvalidArgumentException
	 */
	public function findWordsInCategory($category)
	{
		if (!isset($this->foundWords[$category])) {
			$this->prepareCategoryCalculators();
			foreach (static::$categoriesOrder as $cat) {
				$this->foundWords[$cat] = $this->calculators[$cat]->getFoundWords();
				if ($cat === $category) {
					// nasli jsme pozadovanou kategorii netreba predcasne hledat slova
					// z dalsich kategorii
					break;
				}
			}
		}
		if (!isset($this->foundWords[$category])) {
			throw new \InvalidArgumentException('Unknown category \'' . $category . '\'.');
		}

		return $this->foundWords[$category];
	}


	/**
	 * @return IFoundWord[]
	 */
	public function findWords()
	{
		foreach (static::$categoriesOrder as $cat) {
			$this->findWordsInCategory($cat);
		}
		return $this->foundWords;
	}


	/**
	 * Prepare calculators.
	 */
	protected function prepareCategoryCalculators()
	{
		if ($this->calculators === null) {
			$optimizer = $this->createOptimizer();
			$this->calculators = [
				ISentenceScoringService::CATEGORY_0 => new ScoringCategoryCalculator($optimizer, $this->dictionary,
					ISentenceScoringService::CATEGORY_0),
				ISentenceScoringService::CATEGORY_1 => new ScoringCategoryCalculator($optimizer, $this->dictionary,
					ISentenceScoringService::CATEGORY_1),
				ISentenceScoringService::CATEGORY_2 => new ScoringCategoryCalculator($optimizer, $this->dictionary,
					ISentenceScoringService::CATEGORY_2),
				ISentenceScoringService::CATEGORY_3 => new ScoringCategoryCleanupCalculator($optimizer,
					$this->dictionary, ISentenceScoringService::CATEGORY_3),
				ISentenceScoringService::CATEGORY_4 => new ScoringCategoryCalculator($optimizer, $this->dictionary,
					ISentenceScoringService::CATEGORY_4),
				ISentenceScoringService::CATEGORY_5 => new ScoringCategoryCalculator($optimizer, $this->dictionary,
					ISentenceScoringService::CATEGORY_5),
			];
		}
	}


	/**
	 * @return Optimizer
	 */
	protected function createOptimizer()
	{
		return new Optimizer($this->getPreparedSentence(), $this->dictionary);
	}
}
