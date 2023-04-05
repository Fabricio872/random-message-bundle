<?php

namespace Fabricio872\RandomMessageBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Fabricio872\RandomMessageBundle\Model\MessageModel;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\RuntimeExtensionInterface;

class RandomMessage implements RuntimeExtensionInterface
{
    public function __construct(
        private string              $path,
        private SerializerInterface $serializer
    )
    {
    }

    /**
     * Returns random message
     *
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->getMessages()->get(rand(0, $this->getMessages()->count() - 1));
    }

    private static function getFiles(string $path): array
    {
        $files = [];
        $dirs = scandir($path);
        $dirs = array_splice($dirs, 2);
        foreach ($dirs as $dir) {
            $dir = $path . DIRECTORY_SEPARATOR . $dir;
            if (is_dir($dir)) {
                $files = array_merge($files, self::getFiles($dir));
            } elseif (is_file($dir)) {

                $files[] = $dir;
            }
        }
        return $files;
    }

    /**
     * @return ArrayCollection<int, MessageModel>
     */
    private function getMessages(): ArrayCollection
    {
        $messages = new ArrayCollection();
        foreach (self::getFiles($this->path) as $filePath) {
            /** @var MessageModel $model */
            $model = $this->serializer->deserialize(file_get_contents($filePath), MessageModel::class, 'json');
            dump($model);
            foreach ($model->getMessages() as $message) {
                $messages->add($message);
            }
        }
        return $messages;
    }
}
