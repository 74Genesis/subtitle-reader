<?php
namespace genesis\SubtitleReader\Format;
use genesis\SubtitleReader\exception\ParsingException;

/**
 * Class for working with an srt format
 */
class Srt extends Format
{

	/**
	 * Parses subtitles (divides them into blocks)
	 *
	 * @param      string  $content  Subtitles
	 */
	protected function parse($rawSubtitles = "")
	{
		$blocks = preg_split("/\R{2}/m", $rawSubtitles);
		foreach ($blocks as $block) {
			$data = $this->parseBlock($block);
			if ($data !== false) {
				$this->subtitles[] = $data;
			}
		}
	}

	/**
	 * Parses one subtitle block
	 * Returns array with timecodes and text of subtitle.
	 * Returns false if parsing failed.
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
	 * Divides text into lines
	 *
	 * @param      string  $text   The text
	 *
	 * @return     array  Array with lines
	 */
	public function parseRows($text)
	{
		return preg_split("/\R/m", trim($text));
	}
}