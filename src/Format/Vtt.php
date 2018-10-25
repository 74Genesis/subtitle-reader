<?php
namespace genesis\SubtitleReader\Format;
use genesis\SubtitleReader\exception\ParsingException;

/**
 * Class for working with an srt format
 */
class Vtt extends Format
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
		$matches = [];

		preg_match("/((?:\d{2}:){1,2}\d{2}\.\d{3}).-->.((?:\d{2}:){1,2}\d{2}\.\d{3}).*\R((?:.|\R)*)/", $subtitleBlock, $matches);
		if (count($matches) < 4) return false;
		
		$timeStart = $this->parseTimecode($matches[1]);
		$timeEnd = $this->parseTimecode($matches[2]);
		$text = $this->parseRows(htmlspecialchars($matches[3]));

		return [
			'start' => $timeStart,
			'end' => $timeEnd,
			'text' => $text,
		];
	}

	/**
	 * Parses timecode. Vtt has two formats of timecode
	 * With hours and without. "mm:ss.ttt" or "hh:mm:ss.ttt"
	 *
	 * @param      string   $timecode  The timecode
	 *
	 * @return     integer  returns time in internal format 
	 */
	private function parseTimecode($timecode)
	{
		$time = [];
		$h = ""; 
		$m = ""; 
		$s = ""; 
		$ms = "";
	    
	    $timecode = str_replace(".", ":", $timecode);
	    $time = explode(":", $timecode);
	    $time = array_reverse($time);

	    if (count($time) < 3) return false;

	    $ms = $time[0];
	    $s = $time[1];
	    $m = $time[2];
	    isset($time[3]) ? $h = $time[3] : $h = "00";

	    return $this->timeToLocalFormat($h, $m, $s, $ms);
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