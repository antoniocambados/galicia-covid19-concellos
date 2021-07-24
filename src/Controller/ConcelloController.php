<?php

namespace App\Controller;

use App\UseCase\GetConcelloData;
use App\UseCase\GetConcelloDataMessage;
use App\UseCase\GetConcellos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConcelloController extends AbstractController
{
    protected GetConcelloData $concelloDataUseCase;

    protected GetConcellos $concellosUseCase;

    public function __construct(GetConcelloData $concelloDataUseCase, GetConcellos $concellosUseCase)
    {
        $this->concelloDataUseCase = $concelloDataUseCase;
        $this->concellosUseCase    = $concellosUseCase;
    }

    public function __invoke(string $concello)
    {
        $concellos      = $this->concellosUseCase->__invoke();
        $municipiosData = $this->concelloDataUseCase->__invoke(new GetConcelloDataMessage($concello));

        return $this->render('concello.html.twig', [
            'concello'  => $concello,
            'concellos' => $concellos,
            'data'      => $municipiosData,
        ]);
    }
}