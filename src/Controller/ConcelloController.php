<?php

namespace App\Controller;

use App\UseCase\GetConcellosUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

class ConcelloController extends AbstractController
{
    protected GetConcellosUseCase $concellosUseCase;
    protected string $publicPath;

    public function __construct(GetConcellosUseCase $concellosUseCase, string $publicPath)
    {
        $this->concellosUseCase = $concellosUseCase;
        $this->publicPath       = $publicPath;
    }

    public function __invoke(string $concello)
    {
        $concellos = $this->concellosUseCase->__invoke();

        return $this->render('concello.html.twig', [
            'concello'   => $concello,
            'concellos'  => $concellos,
            'updateDate' => $this->getUpdateDate($concello),
        ]);
    }

    private function getUpdateDate(string $concello): ?\DateTime
    {
        $path = sprintf('%s/data/%s.json', rtrim($this->publicPath, '/'), $concello);
        
        if (file_exists($path)) {
            return (new \DateTime())->setTimestamp(filemtime($path));
        }

        return null;
    }
}