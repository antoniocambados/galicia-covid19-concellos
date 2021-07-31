<?php

namespace App\UseCase;

use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class BuildConcellosDataUseCase
{
    protected GetConcellosUseCase $concellosUseCase;
    private string $publicPath;
    private string $repoPath;

    public function __construct(GetConcellosUseCase $concellosUseCase, string $publicPath, string $repoPath)
    {
        $this->concellosUseCase = $concellosUseCase;
        $this->publicPath       = $publicPath;
        $this->repoPath         = $repoPath;
    }

    public function __invoke(): void
    {
        $concellos = [];
        $headers   = [
            // "fecha", "codigo_municipio", "municipio", "habitantes", 
            // "casos_14d", "casos_14d_min", "casos_14d_max", "IA14", "IA14_min", "IA14_max", 
            // "casos_7d", "casos_7d_min", "casos_7d_max", "IA7", "IA7_min", "IA7_max",
            "fecha", "municipio", "habitantes", "casos_14d", "casos_7d",
        ];

        $finder = new Finder();
        $finder->files()->in(sprintf('%s/incidencia-municipios', rtrim($this->repoPath, '/')));
        $this->clearOutputPath();

        foreach ($finder as $file) {
            $reader = Reader::createFromPath($file->getRealPath(), 'r');
            $reader->setHeaderOffset(0);

            foreach ($reader->getRecords() as $record) {
                $sanitizedRecord = [];
                foreach ($headers as $header) {
                    $sanitizedRecord[$header] = array_key_exists($header, $record) && $record[$header] !== 'NA' ? $record[$header] : null;
                }
                $concellos[$sanitizedRecord['municipio']][] = $sanitizedRecord;
            }
        }

        foreach ($concellos as $nome => $rows) {
            $csvFile  = $this->getOutputPath(sprintf('%s.csv', $nome));
            $jsonFile = $this->getOutputPath(sprintf('%s.json', $nome));

            file_put_contents($jsonFile, json_encode($rows, JSON_PRETTY_PRINT));
            $writer = Writer::createFromPath($csvFile, 'w');
            $writer->insertOne($headers);

            foreach ($rows as $row) {
                $writer->insertOne(array_values($row));
            }
        }
    }

    private function getOutputPath(string $filename)
    {
        $folder = $this->getOutputFolder();

        if (!file_exists($folder)) {
            mkdir($folder, 0644, true);
        }

        return sprintf('%s/%s', rtrim($folder, '/'), $filename);
    }

    private function getOutputFolder(): string
    {
        return sprintf('%s/data', rtrim($this->publicPath, '/'));
    }

    private function clearOutputPath(): void 
    {
        $folder = $this->getOutputFolder();
        $fs     = new Filesystem();

        if ($fs->exists($folder)) {
            $fs->remove($folder);
        }
    }
}