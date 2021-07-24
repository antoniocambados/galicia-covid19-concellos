<?php

namespace App\Command;

use App\UseCase\BuildConcellosDataUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildConcellosDataCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:build-data';
    protected BuildConcellosDataUseCase $buildUseCase;

    public function __construct(BuildConcellosDataUseCase $buildUseCase)
    {
        parent::__construct();
        
        $this->buildUseCase = $buildUseCase;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->buildUseCase->__invoke();
        return Command::SUCCESS;
    }
}