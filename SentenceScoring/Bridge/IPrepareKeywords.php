<?php

namespace Tools\SentenceScoring\Bridge;

/**
 * Interface IPrepareKeywords.
 */
interface IPrepareKeywords
{
	/**
	 * Prevede/odstrani nevalidni ascii znaky.
	 *
	 * @param string $sentence
	 *
	 * @return string
	 */
	public function stripToKeywords($sentence);
}
