<?php

namespace Tools\SentenceScoring\Search;

use Tools\SentenceScoring\Bridge\IPrepareKeywords;
use Tools\SentenceScoring\Dictionary\IDictionaryFactory;

/**
 * Class SearchFactory.
 */
class SearchFactory implements ISearchFactory
{
	/**
	 * @var IDictionaryFactory
	 */
	protected $dictionaryFactory;

	/**
	 * @var IPrepareKeywords
	 */
	protected $keywordPrepare;

	/**
	 * @var ISearch[]
	 */
	protected $searches = [];


	/**
	 * @param IDictionaryFactory $dictionaryFactory
	 * @param IPrepareKeywords $keywordPrepare
	 */
	public function __construct(IDictionaryFactory $dictionaryFactory, IPrepareKeywords $keywordPrepare)
	{
		$this->dictionaryFactory = $dictionaryFactory;
		$this->keywordPrepare = $keywordPrepare;
	}


	/**
	 * @param string $sentence
	 *
	 * @return ISearch
	 */
	public function getSearch($sentence)
	{
		if (!isset($this->searches[$sentence])) {
			$this->searches[$sentence] = new Search(
				$this->dictionaryFactory->getDictionary(),
				$this->keywordPrepare,
				$sentence
			);
		}

		return $this->searches[$sentence];
	}
}
