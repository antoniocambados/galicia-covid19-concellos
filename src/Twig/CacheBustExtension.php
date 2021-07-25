<?php

namespace App\Twig;

use Gitonomy\Git\Repository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CacheBustExtension extends AbstractExtension
{
    private string $repoPath;
    private ?string $commit;

    public function __construct(string $repoPath)
    {
        $this->repoPath = $repoPath;
        $this->commit   = null;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('bust', [$this, 'bust']),
        ];
    }

    public function bust(string $url): string
    {
        if (!$this->commit) {
            $repository   = new Repository($this->repoPath);
            $commit       = $repository->getHeadCommit();
            $this->commit = $commit ? $commit->getHash() : null;
        }
        
        if ($this->commit) {
            $bust      = sprintf('v=%s', $this->commit);
            $separator = stripos($url, '?') !== false ? '&' : '?';

            return sprintf('%s%s%s', $url, $separator, $bust);
        }

        return $url;
    }
}
