<?php

namespace App\Controller;

use App\UseCase\GetConcellos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    protected GetConcellos $concellosUseCase;

    public function __construct(GetConcellos $concellosUseCase)
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