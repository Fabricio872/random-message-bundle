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
        private readonly string              $path,
        private readonly string              $defaultLanguage,
        private readonly SerializerInterface $serializer
    )
    {
    }

    /**
     * Returns random message
     *
     * @param string|null $language
     * @return MessageModel|null
     * @throws Exception
     */
    public function getMessage(string $language = null): ?MessageModel
    {
        $messageCollection = $this->getAllMessages($language ?? $this->defaultLanguage);
        return $messageCollection->get(random_int(0, $this->getAllMessages($language ?? $this->defaultLanguage)->count() - 1));
    }

    /**
     * @return ArrayCollection<int, MessageModel>
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

    public static function getFiles(string $path): array
    {
        $files = [];
        $dirs = scandir($path);
        $dirs = array_splice($dirs, 2);
        foreach ($dirs as $dir) {
            if (!in_array($dir, ['.git'], true)) {
                $dir = $path . DIRECTORY_SEPARATOR . $dir;
                if (is_dir($dir)) {
                    $files = array_merge($files, self::getFiles($dir));
                } elseif (is_file($dir)) {
                    $files[] = $dir;
                }
            }
        }
        return $files;
    }
}
