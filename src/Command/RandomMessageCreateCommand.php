<?php

namespace Fabricio872\RandomMessageBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Fabricio872\RandomMessageBundle\Model\MessageModel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('category', InputArgument::OPTIONAL, 'Category for messages e.g. dad jokes, inspirational quotes')
            ->addArgument('lang', InputArgument::OPTIONAL, 'Define language')
            ->addOption('nsfw', 'N', InputOption::VALUE_OPTIONAL, 'Mark as NSFW. If not present this will be asked on every message');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $category = $input->getArgument('category');

        if (!$category) {
            $category = $this->io->ask('Category for messages e.g. dad jokes, inspirational quotes');
        }
        $category = u($category)->snake();

        $lang = $input->getArgument('lang');
        if (!$lang) {
            $lang = $this->getLanguage();
        }

        $nsfw = $input->getOption('nsfw');

        $filePath = $this->path . DIRECTORY_SEPARATOR . $category . '.json';
        do {
            $messages = new ArrayCollection();
            if (file_exists($filePath)) {
                foreach ($this->serializer->deserialize(file_get_contents($filePath), MessageModel::class . '[]', 'json') as $message) {
                    $messages->add($message);
                }
            }
            $rawMessage = $this->io->ask('Add message. Leave empty to stop');
            if ($rawMessage) {
                $message = new MessageModel();

                $message->setMessage($rawMessage);

                if ($lang) {
                    $message->setLang($lang);
                } else {
                    $message->setLang($this->getLanguage());
                }

                if (!is_null($nsfw)) {
                    $message->setIsNsfw($nsfw);
                } else {
                    $message->setIsNsfw($this->io->ask('Mark as NSFW [y/n]', 'n') == 'y');
                }

                if ($this->validator->validate($message)->count()) {
                    foreach ($this->validator->validate($message) as $violation) {
                        $this->io->warning($violation->getMessage());
                    }
                } else {
                    $messages->add($message);
                }
            }

            if (!file_exists($this->path)){
                mkdir($this->path);
            }
            file_put_contents($filePath, $this->serializer->serialize($messages, 'json'));
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
}
