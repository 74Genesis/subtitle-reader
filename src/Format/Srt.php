<?php
namespace genesis\SubtitleReader\Format;
use genesis\SubtitleReader\exception\ParsingException;

/**
 * Class for working with an srt format
 */
class Srt extends Format
{

	/**
	 * {@inheritdoc}
	 * @param      string  $rawSubtitles
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

		preg_match("/(\d{2}):(\d{2}):(\d{2}),(\d{3}).-->.(\d{2}):(\d{2}):(\d{2}),(\d{3})((?:.|\R)*)/", $subtitleBlock, $matches);

		if (count($matches) < 10) return false;

		$timeStart = $this->timeToLocalFormat($matches[1], $matches[2], $matches[3], $matches[4]);
		$timeEnd = $this->timeToLocalFormat($matches[5], $matches[6], $matches[7], $matches[8]);

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
	private function parseRows($text)
	{
		$rows = preg_split("/\R/m", trim($text));
		foreach ($rows as $key => $row) {
			$rows[$key] = trim($row);
		}
		return $rows;
	}
}