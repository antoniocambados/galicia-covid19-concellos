<?php

namespace App\Controller;

use App\UseCase\GetConcellosUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConcelloController extends AbstractController
{
    protected GetConcellosUseCase $concellosUseCase;

    public function __construct(GetConcellosUseCase $concellosUseCase)
    {
        $this->concellosUseCase = $concellosUseCase;
    }

    public function __invoke(string $concello)
    {
        $concellos = $this->concellosUseCase->__invoke();

        return $this->render('concello.html.twig', [
            'concello'  => $concello,
            'concellos' => $concellos,
        ]);
    }
}