<?php

namespace Tools\SentenceScoring\Search;

/**
 * Interface ISearchFactory.
 */
interface ISearchFactory
{
	/**
	 * @param string $sentence
	 *
	 * @return ISearch
	 */
	public function getSearch($sentence);
}
