<?php

namespace App\UseCase;

use Gitonomy\Git\Admin;
use Gitonomy\Git\Repository;

class UpdateSourceDataUseCase
{
    const SOURCE_REPO = 'https://github.com/lipido/galicia-covid19.git';

    private string $repoPath;

    public function __construct(string $repoPath)
    {
        $this->repoPath = $repoPath;
    }

    public function __invoke()
    {
        if (!file_exists($this->repoPath)) {
            $repository = Admin::cloneTo($this->repoPath, self::SOURCE_REPO, false);
        } else {
            $repository = new Repository($this->repoPath);
        }

        $repository->run('fetch', ['--all']);
        $repository->run('pull');
    }
}
