<?php

declare(strict_types=1);

namespace Fabricio872\RandomMessageBundle\Traits;

trait QuestionsTrait
{
    protected function pickRepo(): string
    {
        foreach ($this->repositories as $id => $repository) {
            $this->io->writeln(sprintf('[%s] %s', $id, $repository));
        }

        return $this->repositories[$this->io->ask('Pick repository', 0)];
    }
}
