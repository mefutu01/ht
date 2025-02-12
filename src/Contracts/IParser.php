<?php

namespace M3futu\Ht\Contracts;

interface IParser
{ 
    public function parse(string $filePath): array;
}