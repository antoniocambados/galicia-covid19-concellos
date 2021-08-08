<?php

namespace App\Controller;

use App\UseCase\GetConcellosUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{
    protected GetConcellosUseCase $concellosUseCase;

    public function __construct(GetConcellosUseCase $concellosUseCase)
    {
        $this->concellosUseCase = $concellosUseCase;
    }

    public function __invoke(): Response
    {
        $concellos = $this->concellosUseCase->__invoke();

        return $this->render('homepage.html.twig', [
            'concellos' => $concellos,
        ]);
    }
}