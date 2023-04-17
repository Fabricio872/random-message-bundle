<?php

declare(strict_types=1);

namespace Fabricio872\RandomMessageBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Fabricio872\RandomMessageBundle\Model\MessageModel;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\RuntimeExtensionInterface;

class RandomMessage implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly string $path,
        private readonly string $defaultLanguage,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Returns random message
     *
     * @param string|null $language
     * @throws Exception
     */
    public function getMessage(string $language = null): ?string
    {
        $messageCollection = $this->getAllMessages($language ?? $this->defaultLanguage);
        $totalMessages = $this->getAllMessages($language ?? $this->defaultLanguage)->count();
        if ($totalMessages - 1 <= 0) {
            throw new Exception('Variable cannot be negative number');
        }
        return $messageCollection->get(random_int(0, $totalMessages - 1));
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getAllMessages(string $language = null): ArrayCollection
    {
        $messages = new ArrayCollection();
        foreach (self::getFiles($this->path) as $filePath) {
            $model = $this->getModel($filePath);

            if ($model) {
                foreach ($model->getMessages() as $message) {
                    if ($language ?? $this->defaultLanguage === $model->getLanguage()) {
                        $messages->add($message);
                    }
                }
            }
        }
        return $messages;
    }

    public function getModel(string $filePath): ?MessageModel
    {
        try {
            return $this->serializer->deserialize(file_get_contents($filePath), MessageModel::class, 'json');
        } catch (NotEncodableValueException) {
            return null;
        }
    }

    /**
     * @return array<int, string>
     */
    public static function getFiles(string $path): array
    {
        $files = [];
        $dirs = scandir($path);
        if (! $dirs) {
            throw new Exception(sprintf('Cannot find dir in: "%s"', $path));
        }
        $dirs = array_splice($dirs, 2);
        foreach ($dirs as $dir) {
            if (! in_array($dir, ['.git'], true)) {
                $dir = $path . DIRECTORY_SEPARATOR . $dir;
                if (is_dir($dir)) {
                    $files = [...$files, ...self::getFiles($dir)];
                } elseif (is_file($dir)) {
                    $files[] = $dir;
                }
            }
        }
        return $files;
    }
}
