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
     * Array with subtitles
     *
     * @var        array
     */
    protected $subtitles = [];

    /**
     * Gets format name and returns object for working with current format
     *
     * @param      string  $formatClass  The format name
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
     * @param      string   $path   File path.
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
     * Converts time to internal format
     *
     * @param      string  $h      hours
     * @param      string  $m      minutes
     * @param      string  $s      seconds
     * @param      string  $ms     milliseconds
     *
     * @return     integer         time in internal format (seconds and milliseconds)
     */
    public function timeToLocalFormat($h = "", $m = "", $s = "", $ms = "")
    {
        $h = (float) $h;
        $m = (float) $m;
        $s = (float) $s;
        $ms = (float) ("0." . $ms);

        return ($h * 3600 + $m * 60 + $s + $ms);
    }

    /**
     * Parses subtitiles.
     *
     * @param     string  $rawSubtitles  String with subtitles
     */
    abstract protected function parse($rawSubtitles);

    /**
     * Saves subtitles to file .
     *
     * @param      string   $path   The file path
     *
     * @return     boolean  Returns true if saveing was successful
     */
    abstract public function saveToFile($path);

    /**
     * Returns the subtitles array.
     *
     * @return     array.
     */
    public function getArray() 
    {
        return $this->subtitles;
    }

    /**
     * Sets the subtitles array.
     *
     * @param      array  $subtitles  The subtitles array
     */
    public function setArray($subtitles) 
    {
        $this->subtitles = $subtitles;
    }
}