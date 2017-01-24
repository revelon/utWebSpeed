<?php

namespace Tools\SentenceScoring\Result;

/**
 * Interface IScoringResult.
 */
interface IScoringResult
{
	/**
	 * @return int
	 */
	public function getScore();


	/**
	 * @return string
	 */
	public function getHardcoreLevel();
}
