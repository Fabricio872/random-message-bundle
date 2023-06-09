<?php

declare(strict_types=1);

namespace Fabricio872\RandomMessageBundle\Service;

use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use Exception;

class GitService
{
    final public const GIT_CLONE = 0;

    final public const GIT_PULL = 1;

    final public const GIT_NOTHING = 2;

    private readonly Git $git;

    public function __construct(
        private readonly string $path
    ) {
        $this->git = new Git();
    }

    public function getPath(string $repository): string
    {
        $parsedUrl = parse_url($repository);
        if (! isset($parsedUrl['path'])) {
            throw new Exception('Cannot parse url');
        }
        $exploded = explode('/', $parsedUrl['path']);

        if (! isset($exploded[1]) || ! isset($exploded[2])) {
            throw new Exception(sprintf('Invalid url "%s"', $repository));
        }

        return $this->path . '/' . $exploded[1] . '/' . $exploded[2];
    }

    public function updateRepo(string $repo): int
    {
        try {
            $this->git->cloneRepository($repo, $this->getPath($repo));
            return self::GIT_CLONE;
        } catch (GitException $exception) {
            if (! str_contains($exception->getMessage(), 'Repo already exists')) {
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

    /**
     * @return array<int, string>|null
     * @throws GitException
     */
    public function repoChanges(string $repo): ?array
    {
        $repository = $this->git->open($this->getPath($repo));

        if ($repository->hasChanges()) {
            return $repository->execute('status', '--porcelain');
        }
        return null;
    }

    public function makePullRequest(string $repo, string $commitMessage, string $gitEmail, string $gitName, string $accessToken): ?string
    {
        $this->updateRepo($repo);
        $repository = $this->git->open($this->getPath($repo));

        if ($repository->hasChanges()) {
            $repository->addAllChanges();
            $repository->execute('config', 'user.email', $gitEmail);
            $repository->execute('config', 'user.name', $gitName);
            $repository->commit($commitMessage);

            $repository->push(
                sprintf(
                    'https://%s:%s@%s',
                    $gitName,
                    $accessToken,
                    substr(
                        $repo,
                        strlen('https://')
                    )
                )
            );

            return "Repositroy pushed.";
        }
        return null;
    }
}
