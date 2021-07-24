<?php

namespace App\UseCase;

use App\UseCase\GetConcelloDataMessage;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class GetConcelloDataUseCase
{
    private string $publicPath;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $projectDir       = (string) $parameterBag->get('kernel.project_dir');
        $this->publicPath = $projectDir . '/public';
    }

    public function __invoke(GetConcelloDataMessage $message): array
    {
        // $cacheTimeout = time() - (1 * 60 * 60);
        $municipios   = [];
        $headers      = [
            "fecha", "codigo_municipio", "municipio", "habitantes", 
            "casos_14d", "casos_14d_min", "casos_14d_max", "IA14", "IA14_min", "IA14_max", 
            "casos_7d", "casos_7d_min", "casos_7d_max", "IA7", "IA7_min", "IA7_max",
        ];
        $output = $this->getOutputPath($message);

        // if (file_exists($output) && filemtime($output) >= $cacheTimeout) {
        if (file_exists($output)) {
            $reader = Reader::createFromPath($output, 'r');
            $reader->setHeaderOffset(0);

            return array_values(iterator_to_array($reader->getRecords()));
        }

        $writer = Writer::createFromPath($output, 'w');
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../../../galicia-covid19/incidencia-municipios');
        $writer->insertOne($headers);

        foreach ($finder as $file) {
            $reader = Reader::createFromPath($file->getRealPath(), 'r');
            $reader->setHeaderOffset(0);

            $stmt = Statement::create()
                ->limit(1)
                ->where(fn(array $record) => $record['municipio'] === $message->concello);

                foreach ($stmt->process($reader) as $record) {
                    $municipio = [];
                    foreach ($headers as $header) {
                        $municipio[$header] = array_key_exists($header, $record) && $record[$header] !== 'NA' ? $record[$header] : null;
                    }

                    $writer->insertOne(array_values($municipio));
                    $municipios[] = $municipio;
                }
        }

        return $municipios;
    }

    private function getOutputPath(GetConcelloDataMessage $message)
    {
        $folder = sprintf('%s/municipios', rtrim($this->publicPath, '/'));

        if (!file_exists($folder)) {
            mkdir($folder, 0644, true);
        }

        return sprintf('%s/%s.csv', rtrim($folder, '/'), $message->concello);
    }
}