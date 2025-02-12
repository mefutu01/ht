<?php

namespace M3futu\Ht;

use M3futu\Ht\Models\RequestData;

class AnalyzerService
{
    private $dates = [];

    /**
     * Анализирует данные посещаемости
     * @param array<RequestData> $records
     * @return array[][][]
     */
    public function sort(array $records)
    {
        /**
         * @var RequestData
         */
        foreach ($records as $key => $record) {

            if (!is_a($record, RequestData::class)) {
                throw new \InvalidArgumentException("Элемент массива должен быть оъектом класса " . RequestData::class);
            }

            $date = gmdate('Y-m-d', $record->time);

            // Если надо учитывать без query параметров 
            // $path = parse_url($record->url, PHP_URL_PATH);
            $path = rtrim($record->url, '/');

            if (isset($this->dates[$date][$path])) {
                $this->dates[$date][$path]['count']++;
                $this->dates[$date][$path]['totalDuration'] += $record->duration;
            } else {
                $this->dates[$date][$path] = [
                    'count'         => 1,
                    'totalDuration' => $record->duration,
                    'date'          => $date,
                    'url'           => $record->url,
                ];
            }
        }

        foreach ($this->dates as &$date) {
            usort($date, static function ($left, $right) {
                return $right['count'] - $left['count'];
            });
        }
        return $this->dates;
    }

    /**
     *  Пример вывода
     *  1 +------------+------------------------------+-------+-------+
     *  2 | 2025-02-03 | https://habr.com/ru/feed/all | 10214 | 371ms |
     *  3 |            | https://habr.com/ru/feed/    |  8192 | 713ms |
     *  4 |            | https://habr.com/ru/news/    |  7559 | 620ms |
     *  5 +------------+------------------------------+-------+-------+
     *  6 | 2025-02-04 | https://habr.com/ru/feed/    |  9111 | 404ms |
     *  7 |            | https://habr.com/ru/feed/all |  8782 | 299ms |
     *  8 |            | https://habr.com/ru/news/    |   501 | 608ms |
     *  9 +------------+------------------------------+-------+-------+
     * @return void
     */
    function print(int $recordCount = 3)
    {
        // Определяем ширину колонок
        $columnWidths = [
            'date'          => 12,
            'url'           => 40,
            'count'         => 12,
            'totalDuration' => 12,
        ];

        $header = $footer = sprintf(
            "+%'-{$columnWidths['date']}s+%'-{$columnWidths['url']}s+%'-{$columnWidths['count']}s+%'-{$columnWidths['totalDuration']}s+" . PHP_EOL,
            '',
            '',
            '',
            ''
        );

        $rows = '';
        $prevDate = '';

        foreach ($this->dates as $day) {
            foreach (array_slice($day, 0, $recordCount) as $key => $row) {
                $date = $row['date'] === $prevDate ? '' : $row['date'];
                $prevDate = $row['date'];

                $rows .= sprintf(
                    "|%-{$columnWidths['date']}s|%-{$columnWidths['url']}s|%{$columnWidths['count']}d|%{$columnWidths['totalDuration']}s|" . PHP_EOL,
                    $date,
                    $row['url'],
                    $row['count'],
                    ($row['totalDuration'] / $row['count']) . 'ms'
                );
            }
            $rows .= $header;
        }

        // Выводим таблицу
        echo $header . $rows;
    }
}