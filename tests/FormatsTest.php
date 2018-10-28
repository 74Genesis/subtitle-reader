<?php

use genesis\SubtitleReader\SubtitleReader;
// use genesis\SubtitleReader\Exception\FileException;
use PHPUnit\Framework\TestCase;


class FormatsTest extends TestCase
{
    public function setUp()
    {
        @unlink(__DIR__ . "/subtitles.srt");
    }

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
				"start" => 196331.559,
				"end" => 1,
				"text" => ["Subtiles loaded..."]
			]
		]);
	}

	public function testGettingJson()
	{
		$sr = new SubtitleReader('srt');
		$sr->loadFile(__DIR__."/some.srt");
		$this->assertEquals($sr->getAsJson(), '[{"start":196331.559,"end":1,"text":["Subtiles loaded..."]}]');
	}

	public function testSrtFormat()
	{
		// test loading
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

		// test saving to file
		$sr2 = new SubtitleReader('srt');
		$sr->loadFile(__DIR__ . "/some.srt");
		$file = __DIR__ . "/subtitles.srt";
		$sr->saveAs("srt", $file);
		$content = file_get_contents($file);
	}

	public function testVttFormat()
	{
		$sr = new SubtitleReader('vtt');
		$sr->loadString("
			WEBVTT - Translation of that film I like

			STYLE
			::cue(b) {
			  color: peachpuff;
			}

			1 - title
			00:00:01.000 --> 00:01.000 line:0 position:20%
			Row with <00:17.500> time
			Row 2

			NOTE comment.

			1
			00:00,000 --> 00:00:00.000 line:0 position:20%
			Wrong format
		");
		$this->assertEquals($sr->getAsArray(), [
			[
				"start" => 1,
				"end" => 1,
				"text" => [
					"Row with  time",
					"Row 2"
				]
			]
		]);
	}

    public function tearDown()
    {
        @unlink(__DIR__ . "/subtitles.srt");
    }
}