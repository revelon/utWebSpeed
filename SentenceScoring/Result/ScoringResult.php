<?php

namespace Tools\SentenceScoring\Result;

/**
 * Class ScoringResult.
 */
class ScoringResult implements IScoringResult
{
	/**
	 * @var int
	 */
	protected $score;

	/**
	 * @var string
	 */
	protected $hardcoreLevel;


	/**
	 * @param int $score
	 * @param string $hardcoreLevel
	 */
	public function __construct($score, $hardcoreLevel)
	{
		$this->score = $score;
		$this->hardcoreLevel = $hardcoreLevel;
	}


	/**
	 * @return int
	 */
	public function getScore()
	{
		return $this->score;
	}


	/**
	 * @return string
	 */
	public function getHardcoreLevel()
	{
		return $this->hardcoreLevel;
	}
}
