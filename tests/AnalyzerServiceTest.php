<?php

use PHPUnit\Framework\TestCase;
use M3futu\Ht\AnalyzerService;
use M3futu\Ht\Models\RequestData;

class AnalyzerServiceTest extends TestCase
{
    private $analyzerService;

    protected function setUp(): void
    {
        $this->analyzerService = new AnalyzerService();
    }

    public function testSortWithEmptyRecords()
    {
        $records = [];
        $dates = $this->analyzerService->sort($records);

        $this->assertEmpty($dates);
    }

    public function testSortWithSingleRecord()
    {
        $records = [
            new RequestData(time: strtotime('06.02.2025 +0000'), duration: 312, url: 'https://habr.com/ru/feed/all'),
        ];

        $dates = $this->analyzerService->sort($records);

        $this->assertCount(1, $dates);
        $this->assertArrayHasKey('2025-02-06', $dates);
        $this->assertEquals(1, $dates['2025-02-06'][0]['count']);
        $this->assertEquals(312, $dates['2025-02-06'][0]['totalDuration']);
    }

    public function testSortWithMultipleRecords()
    {
        $records = [
            new RequestData(time: strtotime('06.02.2025 +0000'), duration: 312, url: 'https://habr.com/ru/feed/all'),
            new RequestData(time: strtotime('06.02.2025 +0000'), duration: 198, url: 'https://habr.com/ru/news/'),
            new RequestData(time: strtotime('06.02.2025 +0000'), duration: 236, url: 'https://habr.com/ru/news'),
            new RequestData(time: strtotime('06.02.2025 +0000'), duration: 409, url: 'https://habr.com/ru/feed/all'),
            new RequestData(time: strtotime('06.02.2025 +0000'), duration: 590, url: 'https://habr.com/ru/feed/all'),
            new RequestData(time: strtotime('05.02.2025 +0000'), duration: 590, url: 'https://habr.com/ru/feed/develop'),
        ];

        $dates = $this->analyzerService->sort($records); 

        // Всего дней
        $this->assertCount(2, $dates);

        // Посещений
        $this->assertEquals(3, $dates['2025-02-06'][0]['count']);
        $this->assertEquals(2, $dates['2025-02-06'][1]['count']);

        $this->assertArrayHasKey('2025-02-06', $dates);
        $this->assertArrayHasKey('2025-02-05', $dates);
    }

    public function testSortWithAnotherDataClassThrow()
    {
        $this->expectException(InvalidArgumentException::class);

        $records = [
            new RequestData(time: strtotime('06.02.2025'), duration: 312, url: 'https://habr.com/ru/feed/all'),
            new stdClass(),
        ];

        $dates = $this->analyzerService->sort($records);

    }
}