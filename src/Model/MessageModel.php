<?php

namespace Fabricio872\RandomMessageBundle\Model;

class MessageModel
{
    private string $message = '';
    private bool $isNsfw = false;
    private string $lang = '';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return MessageModel
     */
    public function setMessage(string $message): MessageModel
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNsfw(): bool
    {
        return $this->isNsfw;
    }

    /**
     * @param bool $isNsfw
     * @return MessageModel
     */
    public function setIsNsfw(bool $isNsfw): MessageModel
    {
        $this->isNsfw = $isNsfw;
        return $this;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     * @return MessageModel
     */
    public function setLang(string $lang): MessageModel
    {
        $this->lang = $lang;
        return $this;
    }
}