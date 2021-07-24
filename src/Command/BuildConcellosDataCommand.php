<?php

namespace App\Command;

use App\UseCase\BuildConcellosDataUseCase;
use App\UseCase\UpdateSourceDataUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BuildConcellosDataCommand extends Command
{
    protected static $defaultName = 'app:build-data';
    protected UpdateSourceDataUseCase $updateRepoUseCase;
    protected BuildConcellosDataUseCase $buildUseCase;

    public function __construct(UpdateSourceDataUseCase $updateRepoUseCase, BuildConcellosDataUseCase $buildUseCase)
    {
        parent::__construct();
        
        $this->updateRepoUseCase = $updateRepoUseCase;
        $this->buildUseCase      = $buildUseCase;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Updating source repository');
        $this->updateRepoUseCase->__invoke();
        $io->success('Source repository updated');

        $io->text('Building concello data');
        $this->buildUseCase->__invoke();
        $io->success('Concello data built');

        return Command::SUCCESS;
    }
}