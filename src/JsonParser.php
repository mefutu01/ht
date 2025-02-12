<?php

namespace M3futu\Ht;

use M3futu\Ht\Contracts\IParser;
use M3futu\Ht\Models\RequestData;

class JsonParser implements IParser
{
    public $data;

    /**
     * @var array<RequestData>
     */
    public function parse(string $filePath): array
    {
        $result = [];

        $data = file_get_contents($filePath);

        $records = $this->getRecords($data);

        if (is_array($records)) {
            foreach ($records as $key => $record) {
                $result[] = new RequestData(time: $record['time'], duration: $record['duration'], url: $record['url']);
            }
        }

        return $result;
    }

    function getRecords($input): array
    {
        $jsonValidator = function_exists('\json_validate') ? 'json_validate' : [$this, 'json_validate'];

        if ($jsonValidator($input)) {
            return json_decode($input, 1);
        } else {
            $records = [];
            $pattern = '/\{[^}]+\}/';
            // Ищем все совпадения
            preg_match_all($pattern, $input, $matches);
 
            foreach ($matches[0] as $jsonString) {
                $decoded = json_decode($jsonString, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $records[] = $decoded;
                }
            }
        }
        return $records;
    }

    function json_validate($data)
    {
        json_decode($data, associative: 1);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}