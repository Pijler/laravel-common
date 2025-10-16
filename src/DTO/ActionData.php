<?php

namespace Common\DTO;

use Spatie\LaravelData\Data;

class ActionData extends Data
{
    /**
     * Create a new DTO instance.
     */
    public function __construct(
        public string $url,
        public string $text,
        public array $params = [],
        public string $method = 'get',
    ) {}
}
