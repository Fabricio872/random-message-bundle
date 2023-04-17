<?php

declare(strict_types=1);

namespace Fabricio872\RandomMessageBundle\Traits;

use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;

trait QuestionsTrait
{
    private SymfonyStyle $io;

    protected function pickRepo(): string
    {
        if (!$this->io instanceof SymfonyStyle) {
            throw new Exception('$this->>io variable must be instance of ' . SymfonyStyle::class);
        }

        foreach ($this->repositories as $id => $repository) {
            $this->io->writeln(sprintf('[%s] %s', $id, $repository));
        }

        return $this->repositories[$this->io->ask('Pick repository', '0')];
    }
}
