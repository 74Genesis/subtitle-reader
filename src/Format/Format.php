<?php
namespace genesis\SubtitleReader\Format;

use genesis\SubtitleReader\Exception\FormatNotFoundException;
use genesis\SubtitleReader\Exception\FileException;


/**
 * Basic class for all subtitle formats.
 */
abstract class Format
{

	/**
	 * Gets format name and returns object for working with current format 
	 *
	 * @param      string	formatClass  The format name
	 */
	public static function initial($formatClass)
	{
		$formatClass = "\genesis\SubtitleReader\Format\\" . $formatClass;
		if (class_exists($formatClass)) {
			return new $formatClass();
		}
		throw new FormatNotFoundException("This format is not supported");
	}


	/**
	 * Loads subtitles from a file.
	 *
	 * @param      string	$path   File path.
	 *
	 * @throws     \genesis\SubtitleReader\Exception\FileException
	 */
	public function loadFile($path)
	{
		$content = "";
		if (file_exists($path)) {
			$content = file_get_contents($path);

			if ($content === false) throw new FileException("Can't get content from file");
			$this->parse($content);
		} else {
			throw new FileException("File doesn't exists");
		}
	}

	/**
	 * Loads subtitles from a string.
	 *
	 * @param      string  $string  The string
	 */
	public function loadString($string)
	{
		$this->parse($string);
	}

	/**
	 * Parses subtitiles.
	 *
	 * @param     string  $content  String with subtitles
	 */
	abstract protected function parse($content);

	/**
	 * Gets the subtitles array.
	 */
	abstract public function getSubtitlesArray();
}