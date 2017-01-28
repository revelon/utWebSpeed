<?php

ini_set('memory_limit','500M');

if (!$argv[1] || !$argv[2]) {
	exit("\nPlease use in format like: test.php  sentences-to-test-file.csv  library-rules-file.csv\n\n");
}
define('LIBRARY_TO_USE', '../' . $argv[2]);

include 'ISentenceScoringService.php';
include 'ScoringService.php';
include 'ScoringException.php';
include 'Search/ISearchFactory.php';
include 'Search/SearchFactory.php';
include 'Search/ISearch.php';
include 'Search/Search.php';
include 'Dictionary/IDictionaryFactory.php';
include 'Dictionary/DictionaryFactory.php';
include 'Dictionary/IDictionaryPaths.php';
include 'Dictionary/DictionaryPaths.php';
include 'Dictionary/IDictionary.php';
include 'Dictionary/AbstractDictionary.php';
include 'Dictionary/CsvDictionary.php';
include 'Dictionary/IWord.php';
include 'Dictionary/Word.php';
include 'Search/IFoundWord.php';
include 'Search/FoundWord.php';
include 'Bridge/IPrepareKeywords.php';
include 'Optimizer/IOptimizer.php';
include 'Optimizer/Optimizer.php';
include 'Category/IScoringCategoryCalculator.php';
include 'Category/AbstractScoringCalculator.php';
include 'Category/ScoringCategoryCalculator.php';
include 'Category/ScoringCategoryCleanupCalculator.php';
include 'Result/IScoringResult.php';
include 'Result/ScoringResult.php';

class LocalDictionaryPaths extends \Tools\SentenceScoring\Dictionary\DictionaryPaths
{
	//const CSV_FILE_PATH = '../porncheckData.5.csv';
	const CSV_FILE_PATH = LIBRARY_TO_USE;
}

class KeywordPrepareAdapter implements \Tools\SentenceScoring\Bridge\IPrepareKeywords
{
	public static function toAscii($s)
	{
		$s = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s);
		$s = strtr($s, '`\'"^~', "\x01\x02\x03\x04\x05");
		$s = str_replace([
			"\xE2\x80\x9E",
			"\xE2\x80\x9C",
			"\xE2\x80\x9D",
			"\xE2\x80\x9A",
			"\xE2\x80\x98",
			"\xE2\x80\x99",
			"\xC2\xBB",
			"\xC2\xAB",
		],
			["\x03", "\x03", "\x03", "\x02", "\x02", "\x02", '>>', '<<'], $s);
		if (ICONV_IMPL === 'glibc') {
			$s = @iconv('UTF-8', 'WINDOWS-1250//TRANSLIT', $s); // intentionally @
			$s = strtr($s, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
				. "\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
				. "\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
				. "\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe\x96",
				'ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt-');
		} else {
			$s = @iconv('UTF-8', 'ASCII//TRANSLIT', $s); // intentionally @
		}
		$s = str_replace(['`', "'", '"', '^', '~'], '', $s);

		return strtr($s, "\x01\x02\x03\x04\x05", '`\'"^~');
	}

	/**
	 * @param string $sentence
	 *
	 * @return string
	 */
	public function stripToKeywords($sentence)
	{
		// prevede diakritiku na norm pismena pomoci implementace FileModulu
		// (nutne pro zpetnou kompatibilitu po upgradu z Nette 2.2 na 2.3)
		$text = self::toAscii($sentence);

		// vyhodi neceske (a jine) znaky
		$text = preg_replace('/[^a-zA-Z0-9]/', ' ', $text);
		// odmaze nadbytecne mezery
		$text = trim(preg_replace('/\s+/', ' ', $text));

		// nahradi "TrueImage10Home za True Image10Home
		$text = preg_replace('/([a-z])([A-Z])/', '$1 $2', $text);
		// nahradi 10Home za 10 Home (ale Home10 necha bejt)
		$text = preg_replace('/([0-9])([A-Za-z])/', '$1 $2', $text);

		return strtolower($text);
	}
}


$allCategories = ['C0', 'C1', 'C2', 'C3', 'C4', 'C5'];

$prepareKeywords = new KeywordPrepareAdapter();
$dictPaths = new LocalDictionaryPaths();
$dictionaryFactory = new Tools\SentenceScoring\Dictionary\DictionaryFactory($dictPaths, false);
$searchFactory = new Tools\SentenceScoring\Search\SearchFactory($dictionaryFactory, $prepareKeywords);
$scoringService = new Tools\SentenceScoring\ScoringService($searchFactory);
$precision = 3;

echo "\nInput 1: " . $argv[1] . ' , Input 2: ' . $argv[2] . "\n";


$in = explode("\n", trim(file_get_contents($argv[1])));
$report = $asserts = $matches = ['safe' => 0, 'porn' => 0, 'illegal' => 0];
$problems = [];
foreach ($in as $value) {
	$pair = explode("\t", $value);
	if (count($pair)===1) { // try comma instead, as second option as delimiter between asset realm and filename
		$pair = explode(",", $value);
	}
	$asserts[$pair[0]]++;
	$result = $scoringService->analyzeSentence($pair[1]);
	echo 'Input: ' . $pair[1] . "\t";
	echo 'Hardcore level: ' . $result->getHardcoreLevel() . "\t";
	echo 'Score: ' . $result->getScore() . "\n";
	$report[$result->getHardcoreLevel()]++;
	if ($result->getHardcoreLevel() == $pair[0]) {
		$matches[$pair[0]]++;
	} else {

		$l = $searchFactory->getSearch($pair[1]);
		$levels = "";
		foreach ($allCategories as $cat) {
			$words = $l->findWordsInCategory($cat);
			if (count($words)) {
				$levels .= " [" . $cat . " = ";
				foreach($words as $w) {
					$levels .= $w->getWord()->getWord() . ":" . $w->getWord()->getMatchType() . " ";
				}
				$levels .= "]";
			}
		}
		$problems[$pair[0] . ' scored as ' . $result->getHardcoreLevel()][] = trim($pair[1]) . " : score = " . $result->getScore() . $levels;
	}
}

echo "\n  ============== Detected assert sentences from 'file {$argv[1]}': " . count($in) . "  ==============\n";
echo "\n  ============== Rules library used for matching from file: " . LocalDictionaryPaths::CSV_FILE_PATH . "  ==============\n\n";
echo "Problems detected: " . print_r($problems, 1) . "\n\n";
echo "Report of actual score: " . print_r($report, 1) . "  ==============\n\n";
echo "Asserts given: " . print_r($asserts, 1) . "  ==============\n\n";
echo "Matches met: " . print_r($matches, 1) . "  ==============\n";
echo "  ============== Percentage of success: safe = " . round($matches['safe']/($asserts['safe']?:1), $precision)*100 . 
	 " % of {$asserts['safe']} cases, porn = " . round($matches['porn']/($asserts['porn']?:1), $precision)*100 . 
	 " % of {$asserts['porn']} cases, illegal = " . round($matches['illegal']/($asserts['illegal']?:1), $precision)*100 .
	 " % of {$asserts['illegal']} cases  ============== \n\n  ==========  TOTAL SUCCESS RATE = " . 
	 round(($matches['illegal']/($asserts['illegal']?:1) + $matches['porn']/($asserts['porn']?:1) + $matches['safe']/($asserts['safe']?:1))/3, $precision)*100 . 
	 " %  ============\n\n";

/*
$result = $scoringService->analyzeSentence($argv[1]);

echo 'Input: ' . $argv[1];
echo 'Hardcore level: ' . $result->getHardcoreLevel() . "\n";
echo 'Score: ' . $result->getScore() . "\n";
*/

//var_dump($searchFactory->getSearch("hello pretty bitch")->findWordsInCategory('C3'));

/*
L0  single&more words  exact&substring match  (white-list) - substring match on all words
L1  single words       exact&substring match  (often used common words) - substring match on single words only
L2  single&more words  exact&substring match  (illegality coeficients) - substring match on single words only
L3  single&more words  exact&substring match  (typical porno words) - substring match on all words
L4  single&more words  exact&substring match  (porno labels and pornstars) - substring match on all words
L5  single&more words  exact&substring match  (ban keywords) - substring match on single words only
*/

// potiz s rozdelovanim:  ATKGirlfriends  orgasm1 ATKHairy
// exgirlfriends.17.01.06.nika[N1C].mp4 : score = 4 [C1 = nika:2 girl:1 girlfriend:1 exgirlfriend:1
// a tak sex zlobi  teen  young    neparsujeme czech-cabins-73.wmv

