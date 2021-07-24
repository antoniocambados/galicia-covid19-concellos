<?php

namespace App\Controller;

use App\UseCase\GetConcellosUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    protected GetConcellosUseCase $concellosUseCase;

    public function __construct(GetConcellosUseCase $concellosUseCase)
    {
        $this->concellosUseCase = $concellosUseCase;
    }

    public function __invoke()
    {
        $concellos = $this->concellosUseCase->__invoke();

        return $this->render('homepage.html.twig', [
            'concellos' => $concellos,
        ]);
    }
}