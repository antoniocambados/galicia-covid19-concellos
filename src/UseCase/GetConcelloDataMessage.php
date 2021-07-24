<?php

namespace App\UseCase;

class GetConcelloDataMessage
{
    public string $concello = 'O Grove';

    public function __construct(string $concello)
    {
        $this->concello = $concello;
    }
}
