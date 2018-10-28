<?php

namespace genesis\SubtitleReader;

use genesis\SubtitleReader\format\Format;
use genesis\SubtitleReader\exception\FileException;


/**
 * Class for working with subtitles
 */
class SubtitleReader
{
	/**
	 * Oobject for working with subtitles
	 * 
     * @var        Format
	 */
	private $formatObj;
	
	function __construct($format = "")
	{
		$this->setFormat($format);
	}


	/**
	 * Sets the subtitles format.
	 *
	 * @param      string  $format  File extension
	 */
	private function setFormat($format)
	{
		$this->formatObj = Format::initial($format);
	}

	/**
	 * Loads a file.
	 *
	 * @param      string  $path   The path
	 */
	public function loadFile($path)
	{
		$this->formatObj->loadFile($path);
	}

	/**
	 * Loads a string.
	 *
	 * @param      string  $string  The string
	 */
	public function loadString($string)
	{
		$this->formatObj->loadString($string);
	}

	/**
	 * Returns array with subtitles;
	 * Example:
	 *	Array
	 *	(
	 *	    [0] => Array
	 *	        (
	 *	            [start] => 0
	 *	            [end] => 5.208
	 *	            [text] => Array
	 *	                (
	 *	                    [0] => Some text
	 *	                    [1] => Some text row 2
	 *	                )
	 *		        )
	 *	)
	 *
	 * @return     <type>  As array.
	 */
	public function getAsArray()
	{
		return $this->formatObj->getArray();
	}

	/**
	 * Gets the subtitles in json format.
	 *
	 * @return     string  The subtitles json.
	 */
	public function getAsJson()
	{
		return json_encode($this->formatObj->getArray());
	}

	public function saveAs($format = "", $path = "")
	{
		$currentSubtitles = $this->formatObj->getArray();
		$newFormatObj = Format::initial($format);
		$newFormatObj->setArray($currentSubtitles);
		$newFormatObj->saveToFile($path);
	}
}