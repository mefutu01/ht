<?php

namespace M3futu\Ht\Models;

class RequestData
{
    /**
     * Время запроса unix timestamp
     * @var int
     */
    public $time;
    
    /**
     * Время обработки запроса в миллисекундах.
     * @var int
     */
    public $duration;

    /**
     * URL запрашиваемой страницы
     * @var string
     */
    public $url;

    public function __construct(int $time, int $duration, string $url)
    {
        $this->time = $time;
        $this->duration = $duration;
        $this->url = $url;
    }
}