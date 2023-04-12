<?php

namespace Fabricio872\RandomMessageBundle\Command;

use Fabricio872\RandomMessageBundle\Service\GitService;
use Fabricio872\RandomMessageBundle\Traits\QuestionsTrait;
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

    public function __construct(
        private string     $path,
        private array      $repositories,
        private GitService $gitService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        foreach ($this->repositories as $repo) {
            $this->io->writeln(match ($this->gitService->updateRepo($repo)) {
                GitService::GIT_CLONE => sprintf('Repository "%s" cloned', $repo),
                GitService::GIT_PULL => sprintf('Repository "%s" pulled', $repo),
                GitService::GIT_NOTHING => sprintf('Repository "%s" up to date', $repo)
            });
        }

        return Command::SUCCESS;
    }
}
