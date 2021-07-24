<?php

namespace App\UseCase;

use League\Csv\Reader;

class GetConcellosUseCase
{    
    private string $repoPath;

    public function __construct(string $repoPath)
    {
        $this->repoPath = $repoPath;
    }

    public function __invoke(): array
    {
        $reader = Reader::createFromPath(sprintf('%s/scripts/municipios-habitantes.csv', rtrim($this->repoPath, '/')), 'r');
        $reader->setHeaderOffset(0);
        
        return array_values(iterator_to_array($reader->getRecords()));
    }
}
