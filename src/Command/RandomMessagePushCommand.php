<?php

declare(strict_types=1);

namespace Fabricio872\RandomMessageBundle\Command;

use Fabricio872\RandomMessageBundle\Service\GitService;
use Fabricio872\RandomMessageBundle\Traits\QuestionsTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'random_message:push',
    description: 'Command for pushing changes to repository.',
)]
class RandomMessagePushCommand extends Command
{
    private SymfonyStyle $io;

    use QuestionsTrait;

    public function __construct(
        private readonly array $repositories,
        private readonly string $gitEmail,
        private readonly string $gitName,
        private readonly string $gitAccessToken,
        private readonly GitService $gitService
    ) {
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $repo = $this->pickRepo();

        $changes = $this->gitService->repoChanges($repo);
        if (! $changes) {
            $this->io->error(sprintf('No changes for repository %s', $repo));

            return Command::FAILURE;
        }

        $this->io->writeln('Changes:');
        $this->io->listing($changes);

        if ($this->io->ask('Push changes to remote? [y/n]', 'n') === 'y') {
            $commitMessage = $this->io->ask('Describe what you added');
            $message = $this->gitService->makePullRequest($repo, $commitMessage, $this->gitEmail, $this->gitName, $this->gitAccessToken);
            if (! $message) {
                return Command::FAILURE;
            }
            $this->io->success($message);
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
