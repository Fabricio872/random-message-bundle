<?php

declare(strict_types=1);

namespace Fabricio872\RandomMessageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class MessageModel
{
    private ?string $category = null;

    /** @var ArrayCollection<int, string> $messages */
    private ArrayCollection $messages;

    #[Assert\Language]
    private ?string $language = null;

    private ?string $version = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): MessageModel
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getMessages(): ArrayCollection
    {
        return $this->messages;
    }

    /**
     * @param array<int, string> $messages
     */
    public function setMessages(array $messages): MessageModel
    {
        $this->messages = new ArrayCollection($messages);

        return $this;
    }

    /**
     * @param string $message
     */
    public function addMessage(?string $message): MessageModel
    {
        if ($message) {
            $this->messages->add($message);
        }
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(?string $language): MessageModel
    {
        $this->language = $language;
        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(?string $version): MessageModel
    {
        $this->version = $version;
        return $this;
    }
}
