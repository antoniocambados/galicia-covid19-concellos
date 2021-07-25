<?php

namespace App\Twig;

use Gitonomy\Git\Repository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CacheBustExtension extends AbstractExtension
{
    private string $repoPath;

    public function __construct(string $repoPath)
    {
        $this->repoPath = $repoPath;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('bust', [$this, 'bust']),
        ];
    }

    public function bust(string $url)
    {
        $repository = new Repository($this->repoPath);
        $commit     = $repository->getHeadCommit();
        
        if ($commit) {
            $hash      = $commit->getHash();
            $bust      = sprintf('v=%s', $hash);
            $separator = stripos($url, '?') !== false ? '&' : '?';

            return sprintf('%s%s%s', $url, $separator, $bust);
        }

        return $url;
    }
}
