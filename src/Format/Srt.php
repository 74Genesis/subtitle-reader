<?php
namespace genesis\SubtitleReader\Format;
use genesis\SubtitleReader\exception\FileException;

/**
 * Class for working with an .srt format
 */
class Srt extends Format
{

    /**
     * {@inheritdoc}
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

        preg_match("/(\d{2}):(\d{2}):(\d{2}),(\d{3}).-->.(\d{2}):(\d{2}):(\d{2}),(\d{3})((?:.|\R)*)/m", $subtitleBlock, $matches);

        if (count($matches) < 10) return false;

        $timeStart = $this->timeToLocalFormat($matches[1], $matches[2], $matches[3], $matches[4]);
        $timeEnd = $this->timeToLocalFormat($matches[5], $matches[6], $matches[7], $matches[8]);

        $text = $this->parseRows($matches[9]);
        return [
            'start' => $timeStart,
            'end' => $timeEnd,
            'text' => $text,
        ];
    }

    /**
     * Divides text into lines
     * 
     * TODO: replace tags and any danger characters
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

    /**
     * {@inheritdoc}
     */
    public function saveToFile($path)
    {
        $start = "";
        $end = "";
        $lines = "";
        $content = "";

        $number = 1;
        foreach ($this->subtitles as $key => $subtitle) {
            $start = $this->timeToCurrentFormat((float) $subtitle['start']);
            $end = $this->timeToCurrentFormat((float) $subtitle['end']);
            $lines = implode("\r\n", $subtitle['text']);
            $content .= $number . "\r\n" . $start . " --> " . $end . "\r\n" . $lines . "\r\n\r\n";
            $number++;
        }

        $result = file_put_contents($path, $content);

        if ($result === false)
            throw new FileException("File save failed");
        return true;
    }

    /**
     * Converts time for str format (hh:mm:ss,vvv)
     *
     * @param      integer  $raw    time in internal format
     *
     * @return     string   Returns converted time
     */
    public function timeToCurrentFormat($raw)   
    {
        $h = sprintf("%02s", floor($raw / 3600));
        $m = sprintf("%02s", floor($raw / 60 % 60));
        $s = sprintf("%02s", floor($raw % 60));
        $split = explode(".", (string)number_format($raw, 3));

        return $h . ":" . $m . ":" . $s . "," . $split[1];
    }
}