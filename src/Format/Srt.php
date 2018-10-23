<?php
namespace genesis\SubtitleReader\Format;
use genesis\SubtitleReader\exception\ParsingException;

/**
 * Class for working with an srt format
 */
class Srt extends Format
{
	/**
	 * Array with subtitles
	 *
	 * @var        array
	 */
	private $subtitles = [];

	/**
	 * Parses subtitles (divides them into blocks)
	 *
	 * @param      string  $content  Subtitles
	 */
	protected function parse($rawSubtitles = "")
	{
		$blocks = preg_split("/\R{2}/m", $rawSubtitles);
		foreach ($blocks as $key => $subtitleBlock) {
			$this->addToSubtitleArray($subtitleBlock);
		}
	}

	/**
	 *  Adds each block in $subtitles array
	 *
	 * @param      string  $subtitleBlock  The subtitle block
	 */
	private function addToSubtitleArray($subtitleBlock)
	{
		$data = $this->parseBlock($subtitleBlock);
		if ($data !== false) {
			$this->subtitles[] = $data;
		}
	}

	/**
	 * Gets a subtitle block
	 * Returns an array containing text, start time and end time for subtitle
	 *
	 * @param      string        $subtitleBlock  The subtitle block
	 *
	 * @return     array|boolean 
	 */
	private function parseBlock($subtitleBlock)
	{
		$timeStart = 0;
		$timeEnd = 0;
		$text = "";

		preg_match("/(\d{2}):(\d{2}):(\d{2}),(\d{3}).-->.(\d{2}):(\d{2}):(\d{2}),(\d{3})\R((?:.*\R)*.*)/s", $subtitleBlock, $matches);

		if (count($matches) < 10) return false;

		$hourStart = (float) $matches[1];
		$minuteStart = (float) $matches[2];
		$secondStart = (float) $matches[3];
		$msStart = (float) ("0." . $matches[4]);

		$hourEnd = (float) $matches[5];
		$minuteEnd = (float) $matches[6];
		$secondEnd = (float) $matches[7];
		$msEnd = (float) ("0." . $matches[8]);

		$timeStart = $hourStart * 3600 + $minuteStart * 60 + $secondStart + $msStart;
		$timeEnd = $hourEnd * 3600 + $minuteEnd * 60 + $secondEnd + $msEnd;

		$text = $this->parseRows(htmlspecialchars($matches[9]));

		return [
			'start' => $timeStart,
			'end' => $timeEnd,
			'text' => $text,
		];
	}

	/**
	 * divides text into lines
	 *
	 * @param      string  $text   The text
	 *
	 * @return     array  Array with lines
	 */
	public function parseRows($text)
	{
		return preg_split("/\R/m", trim($text));
	}

	/**
	 * Returns the subtitles array.
	 *
	 * @return     array.
	 */
	public function getSubtitlesArray() 
	{
		return $this->subtitles;
	}
}