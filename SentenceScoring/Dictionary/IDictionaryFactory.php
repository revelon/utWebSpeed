<?php

namespace Tools\SentenceScoring\Dictionary;

/**
 * Interface IDictionaryFactory.
 */
interface IDictionaryFactory
{
	/**
	 * @return IDictionary
	 */
	public function getDictionary();
}
