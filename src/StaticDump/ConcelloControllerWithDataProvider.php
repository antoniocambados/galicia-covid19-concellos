<?php

namespace App\StaticDump;

use App\Controller\ConcelloController;
use App\UseCase\GetConcellosUseCase;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;

class ConcelloControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    protected GetConcellosUseCase $concellosUseCase;

    public function __construct(GetConcellosUseCase $concellosUseCase)
    {
        $this->concellosUseCase = $concellosUseCase;
    }

    public function getControllerClass(): string
    {
        return ConcelloController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        $concellos = $this->concellosUseCase->__invoke();

        return array_map(function($item) {
            return $item['municipio'];
        }, $concellos);
    }
}
