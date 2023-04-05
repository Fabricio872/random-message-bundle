<?php

namespace Fabricio872\RandomMessageBundle\Command;

use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'random_message:pull',
    description: 'Command for pulling updates from repositories.',
)]
class RandomMessagePullCommand extends Command
{
    private SymfonyStyle $io;
    private Git $git;
    const GIT_CLONE = 0;
    const GIT_PULL = 1;
    const GIT_NOTHING = 2;

    public function __construct(
        private string $path,
        private array  $repositories
    )
    {
        parent::__construct();
        $this->git = new Git();
    }

    protected function configure(): void
    {
        $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        foreach ($this->repositories as $repo) {
            $this->io->writeln(match ($this->resolveRepo($repo)) {
                self::GIT_CLONE => sprintf('Repository "%s" cloned', $repo),
                self::GIT_PULL => sprintf('Repository "%s" pulled', $repo),
                self::GIT_NOTHING => sprintf('Repository "%s" up to date', $repo)
            });
        }

        return Command::SUCCESS;
    }

    private function getPath(string $repository): string
    {
        $parsedUrl = parse_url($repository);
        $exploded = explode('/', $parsedUrl['path']);

        return $this->path . '/' . $exploded[1] . '/' . $exploded[2];
    }

    private function resolveRepo(string $repo): int
    {
        try {
            $this->git->cloneRepository($repo, $this->getPath($repo));
            return self::GIT_CLONE;
        } catch (GitException $exception) {
            if (!str_contains($exception->getMessage(), 'Repo already exists')) {
                throw $exception;
            }
        }

        $repository = $this->git->open($this->getPath($repo));
        if ($repository->fetch('origin')->hasChanges()) {
            $repository->pull('origin');

            return self::GIT_PULL;
        }
        return self::GIT_NOTHING;
    }
}
