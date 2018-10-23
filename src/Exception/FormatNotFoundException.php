<?php

namespace genesis\SubtitleReader\Exception;

class FormatNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 404, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}