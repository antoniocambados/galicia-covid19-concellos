<?php

namespace App\UseCase;

use League\Csv\Reader;

class GetConcellos
{
    const SOURCE = __DIR__.'/../../../galicia-covid19/scripts/municipios-habitantes.csv';

    public function __invoke(): array
    {
        $reader = Reader::createFromPath(self::SOURCE, 'r');
        $reader->setHeaderOffset(0);
        
        return array_values(iterator_to_array($reader->getRecords()));
    }
}
