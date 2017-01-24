<?php

namespace Tools\SentenceScoring\Dictionary;

/**
 * Interface IDictionaryGenerator.
 */
interface IDictionaryPaths
{
	/**
	 * @param int $version
	 *
	 * @return string
	 */
	public function getCsvFilePath($version);

	/**
	 * @return string
	 */
	public function getGeneratedFilePath();
}
