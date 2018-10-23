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
	private $format;
	
	function __construct($extension = "")
	{
		$this->setFormat($extension);
	}


	/**
	 * Sets the subtitles format.
	 *
	 * @param      string  $extension  File extension
	 */
	public function setFormat($extension)
	{
		$this->format = Format::initial($extension);
	}

	/**
	 * Loads a file.
	 *
	 * @param      string  $path   The path
	 */
	public function loadFile($path)
	{
		$result = $this->format->loadFile($path);
	}

	/**
	 * Loads a string.
	 *
	 * @param      string  $string  The string
	 */
	public function loadString($string)
	{
		$result = $this->format->loadString($string);
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
	public function getSubtitlesArray()
	{
		return $this->format->getSubtitlesArray();
	}


	/**
	 * Gets the subtitles in json format.
	 *
	 * @return     string  The subtitles json.
	 */
	public function getSubtitlesJson()
	{
		return json_encode($this->format->getSubtitlesArray());
	}
}