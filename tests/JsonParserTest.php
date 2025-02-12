<?php

use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    private $jsonParser;

    protected function setUp(): void
    {
        $this->jsonParser = new \M3futu\Ht\JsonParser();
    }

    public function testParseValidJsonFile()
    {

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        $jsonData = json_encode([
            ['time' => 1633072800, 'duration' => 100, 'url' => 'https://habr.com/ru/feed/all'],
            ['time' => 1633072900, 'duration' => 200, 'url' => 'https://habr.com/ru/news/'],
        ]);
        file_put_contents($filePath, $jsonData);

        $result = $this->jsonParser->parse($filePath);


        $this->assertCount(2, $result);
        $this->assertInstanceOf(\M3futu\Ht\Models\RequestData::class, $result[0]);
        $this->assertEquals(1633072800, $result[0]->time);
        $this->assertEquals(100, $result[0]->duration);
        $this->assertEquals('https://habr.com/ru/feed/all', $result[0]->url);


        unlink($filePath);
    }

    public function testParseValidJsonWithoutCommaFile()
    {

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        $jsonData = json_encode([
            ['time' => 1633072800, 'duration' => 100, 'url' => 'https://habr.com/ru/feed/all'],
            ['time' => 1633072900, 'duration' => 200, 'url' => 'https://habr.com/ru/news/'],
        ]);
        $jsonData = str_replace('},{', '}{', $jsonData);
        $jsonData = str_replace(['[',']'], '', $jsonData); 
        file_put_contents($filePath, $jsonData);
        $result = $this->jsonParser->parse($filePath);


        $this->assertCount(2, $result);
        $this->assertInstanceOf(\M3futu\Ht\Models\RequestData::class, $result[0]);
        $this->assertEquals(1633072800, $result[0]->time);
        $this->assertEquals(100, $result[0]->duration);
        $this->assertEquals('https://habr.com/ru/feed/all', $result[0]->url);


        unlink($filePath);
    }

    public function testParseInvalidJsonFile()
    {

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents(filename: $filePath, data: 'invalid json');


        $result = $this->jsonParser->parse($filePath);


        $this->assertEmpty($result);


        unlink($filePath);
    }

    public function testParseEmptyJsonFile()
    {

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, '[]');


        $result = $this->jsonParser->parse($filePath);


        $this->assertEmpty($result);


        unlink($filePath);
    }

    public function testGetRecordsWithValidJson()
    {
        $jsonData = json_encode([
            ['time' => 1633072800, 'duration' => 100, 'url' => 'https://habr.com/ru/feed/all'],
            ['time' => 1633072900, 'duration' => 200, 'url' => 'https://habr.com/ru/news/'],
        ]);

        $result = $this->jsonParser->getRecords($jsonData);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('https://habr.com/ru/feed/all', $result[0]['url']);
    }

    public function testGetRecordsWithInvalidJson()
    {
        $jsonData = 'invalid json';

        $result = $this->jsonParser->getRecords($jsonData);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetRecordsWithEmptyJson()
    {
        $jsonData = '[]';

        $result = $this->jsonParser->getRecords($jsonData);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}