<?php
use App\Lib\ParseStories;

class ParseStoriesTest extends BaseLibTest
{
    public function test_parse_found_match()
    {
        $stories = '[#212] foo';
        $expected = [212];

        $parseStories = new ParseStories();
        $this->assertEquals($expected, $parseStories->parse($stories));

        $stories = <<<STRING
[#123] foo
[ref #1234] foo
[finished #12345] foo
[#123456] foo
[#123456adfa] foo
STRING;
        $expected = [
            123, 1234, 12345, 123456
        ];
        $this->assertEquals($expected, $parseStories->parse($stories));
    }

    public function test_parse_not_found_matches()
    {
        $stories = '[212]';
        $expected = [];

        $parseStories = new ParseStories();
        $this->assertEquals($expected, $parseStories->parse($stories));
    }
}
