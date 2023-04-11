<?php

namespace Fabricio872\RandomMessageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class MessageModel
{
    private ?string $category = null;
    private ArrayCollection $messages;
    private ?bool $isNsfw = null;
    #[Assert\Language]
    private ?string $language = null;
    private ?string $version = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string|null $category
     * @return MessageModel
     */
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
     * @param array $messages
     * @return MessageModel
     */
    public function setMessages(array $messages): MessageModel
    {
        $this->messages = new ArrayCollection($messages);

        return $this;
    }

    /**
     * @param string $message
     * @return MessageModel
     */
    public function addMessage(?string $message): MessageModel
    {
        $this->messages->add($message);
        return $this;
    }

    /**
     * @return bool
     */
    public function isNsfw(): ?bool
    {
        return $this->isNsfw;
    }

    /**
     * @param bool $isNsfw
     * @return MessageModel
     */
    public function setIsNsfw(?bool $isNsfw): MessageModel
    {
        $this->isNsfw = $isNsfw;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return MessageModel
     */
    public function setLanguage(?string $language): MessageModel
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return MessageModel
     */
    public function setVersion(?string $version): MessageModel
    {
        $this->version = $version;
        return $this;
    }
}