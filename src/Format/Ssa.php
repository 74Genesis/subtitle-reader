<?php
namespace genesis\SubtitleReader\Format;
use genesis\SubtitleReader\exception\FileException;
use genesis\SubtitleReader\exception\ParsingException;

/**
 * Class for working with .ssa/.ass format
 */
class Ssa extends Format
{

    /**
     * {@inheritdoc}
     */
    protected function parse($rawSubtitles = "")
    {
        $matches = [];

        preg_match("/\[Events\]\R([\s\S]+)/m", $rawSubtitles, $matches);
        if (!isset($matches[1])) throw new ParsingException("Wrong format. Not found [Events] block");
        
        $blocks = preg_split("/\R/m", $matches[1]);

        $i = 0;     
        $colPos = [];
        foreach ($blocks as $block) {
            if ($i > 0) {
                $data = $this->parseBlock($block, $colPos);
                if ($data !== false) {
                    $this->subtitles[] = $data;
                }
            } else {
                $colPos = $this->findColumnPosition(str_replace("Format:", "", $block));
            }
            $i++;
        }
    }


    /**
     * Finds position of each column in subtitles.
     * String with column names looks like: 
     * Format: Layer, Start, End, Style, Actor, MarginL, MarginR, MarginV, Effect, Text
     *
     * @param      string            $str    The string with column names.
     *
     * @throws     ParsingException  Thrws when there aren't any required columns.
     *
     * @return     array             Array with columns name and its positions.
     */
    public function findColumnPosition($str)
    {
        $colPos = [];
        $cols = explode(",", $str);

        foreach ($cols as $key => $colName) {
            $colName = trim($colName);
            $colPos[$colName] = $key;
        }

        if (
            !isset($colPos['Start']) || 
            !isset($colPos['End']) ||
            !isset($colPos['Text'])
        ) throw new ParsingException("Required columns not found");
        return $colPos;
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
    private function parseBlock($subtitleBlock, $colPos)
    {
        $timeStart = 0;
        $timeEnd = 0;
        $text = "";
        $matches = [];

        $colsCount = count($colPos);
        $matches = explode(",", $subtitleBlock, $colsCount);

        if (count($matches) < 4) return false;
        
        $timeStart = $this->parseTimecode($matches[$colPos['Start']]);
        $timeEnd = $this->parseTimecode($matches[$colPos['End']]);
        $text = explode('\N', $matches[$colPos['Text']]);

        return [
            'start' => $timeStart,
            'end' => $timeEnd,
            'text' => $text,
        ];
    }

    /**
     * Parses timecode from "hh:mm:ss.vvv" format
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

        if (count($time) < 4) return -1;

        $ms = $time[0];
        $s = $time[1];
        $m = $time[2];
        $h = $time[3];

        return $this->timeToLocalFormat($h, $m, $s, $ms);
    } 

    /**
     * {@inheritdoc}
     */
    public function saveToFile($path = "")
    {
        throw new FileException("This function is not supported.");     
    }
}