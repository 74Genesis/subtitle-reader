<?php

use genesis\SubtitleReader\SubtitleReader;
// use genesis\SubtitleReader\Exception\FileException;
use PHPUnit\Framework\TestCase;


class FormatsTest extends TestCase
{
    /**
     * @expectedException genesis\SubtitleReader\Exception\FileException
     */
	public function testFileNotFoundException()
	{
		$sr = new SubtitleReader('srt');
		$sr->loadFile("notfound");
	}

	public function testSuccessfulLoadFile()
	{
		$sr = new SubtitleReader('srt');
		$sr->loadFile(__DIR__."/some.srt");
		$this->assertEquals($sr->getAsArray(), [
			[
				"start" => 0,
				"end" => 1,
				"text" => ["Subtiles loaded..."]
			]
		]);
	}

	public function testSrtFormat()
	{
		$sr = new SubtitleReader('srt');
		$sr->loadString("
			1
			00:00:00,000 --> 01:01:01,001
			Row 1
			Row 2
			Row 3

			2
			00:00:00,000--> 00:00:00,000
			Wrong format

			3
			00:00:00,000 --> 00:00:0,000
			Wrong format
		");
		$this->assertEquals($sr->getAsArray(), [
			[
				"start" => 0,
				"end" => 3661.001,
				"text" => [
					"Row 1",
					"Row 2",
					"Row 3"
				]
			]
		]);
	}
}