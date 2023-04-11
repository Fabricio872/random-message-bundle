<?php

namespace Fabricio872\RandomMessageBundle\Command;

use Composer\InstalledVersions;
use Fabricio872\RandomMessageBundle\Model\MessageModel;
use Fabricio872\RandomMessageBundle\RandomMessage;
use Fabricio872\RandomMessageBundle\Service\GitService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WhiteCube\Lingua\W3cConverter;
use function Symfony\Component\String\u;

#[AsCommand(
    name: 'random_message:create',
    description: 'Command for creating json files with messages.',
)]
class RandomMessageCreateCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private string              $path,
        private array               $repositories,
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator,
        private GitService          $gitService,
        private RandomMessage       $randomMessage
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('category', InputArgument::OPTIONAL, 'Category name for messages e.g. dad jokes, inspirational quotes')
            ->addArgument('lang', InputArgument::OPTIONAL, 'Define language')
            ->addOption('nsfw', 'N', InputOption::VALUE_OPTIONAL, 'Mark as NSFW. If not present this will be asked on every message');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $category = $input->getArgument('category');

        $repo = $this->pickRepo();

        $this->io->writeln(match ($this->gitService->updateRepo($repo)) {
            GitService::GIT_CLONE => sprintf('Repository "%s" cloned', $repo),
            GitService::GIT_PULL => sprintf('Repository "%s" pulled', $repo),
            GitService::GIT_NOTHING => sprintf('Repository "%s" up to date', $repo)
        });

        if (!$category) {
            $question = new Question('Category name for messages e.g. dad jokes, inspirational quotes');
            $question->setAutocompleterValues($this->getCategories($repo));
            $category = $this->io->askQuestion($question);
        }
        $categorySnake = u($category)->snake();

        $filePath = $this->gitService->getPath($repo) . DIRECTORY_SEPARATOR . $categorySnake . '.json';
        $model = new MessageModel();
        if (file_exists($filePath)) {
            /** @var MessageModel $model */
            $model = $this->serializer->deserialize(file_get_contents($filePath), MessageModel::class, 'json');
            $this->io->info(sprintf('Found category with %s messages continuing adding.', $model->getMessages()->count()));
        }
        $model->setCategory($category);

        if (is_null($model->getLanguage())) {
            $lang = $input->getArgument('lang');
            if (!$lang) {
                $model->setLanguage($this->getLanguage());
            }
        }

        if (!is_null($model->isNsfw())) {
            $nsfw = $input->getOption('nsfw');
            if (!is_null($nsfw)) {
                $model->setIsNsfw($nsfw);
            } else {
                $model->setIsNsfw($this->io->ask('Mark as NSFW [y/n]', 'n') == 'y');
            }
        }
        $model->setVersion(InstalledVersions::getVersion('fabricio872/random-message-bundle'));

        if ($this->validator->validate($model)->count()) {
            foreach ($this->validator->validate($model) as $violation) {
                $this->io->warning($violation->getMessage());
            }
            return Command::INVALID;
        } else {
            if (!file_exists($this->path)) {
                mkdir($this->path);
            }
            file_put_contents($filePath, $this->serializer->serialize($model, 'json'));
        }


        do {
            $rawMessage = $this->io->ask('Add message. Leave empty to stop');
            if ($rawMessage) {

                $model->addMessage($rawMessage);

                if ($this->validator->validate($model)->count()) {
                    foreach ($this->validator->validate($model) as $violation) {
                        $this->io->warning($violation->getMessage());
                    }
                    return Command::INVALID;
                }
            }
            file_put_contents($filePath, $this->serializer->serialize($model, 'json'));
        } while ($rawMessage);


        return Command::SUCCESS;
    }

    private function getLanguage(): string
    {
        $lang = $this->io->ask('Define language');
        while (!W3cConverter::check($lang)) {
            $this->io->warning(sprintf('Language "%s" has wrong format. Use only to letter naming schema e.g. "en"', $lang));
            $lang = $this->io->ask('Define language', 'en');
        }
        return $lang;
    }

    private function pickRepo(): string
    {
        foreach ($this->repositories as $id => $repository) {

            $this->io->writeln(sprintf('[%s] %s', $id, $repository));
        }

        return $this->repositories[$this->io->ask('Pick repository', 0)];
    }

    private function getCategories(string $repo)
    {
        $categories = [];
        foreach (RandomMessage::getFiles($this->gitService->getPath($repo)) as $item) {
            $model = $this->randomMessage->getModel($item);
            if ($model) {
                $categories[] = $model->getCategory();
            }
        }
        return $categories;
    }
}
