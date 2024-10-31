<?php

class TelegramPostChat
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $chatId;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $deletedAt;

    /**
     * @param array $chatInfo
     */
    public function __construct(array $chatInfo)
    {
        $this->id = $chatInfo['id'];
        $this->name = $chatInfo['name'];
        $this->chatId = $chatInfo['chat_id'];
        $this->createdAt = $chatInfo['created_at'];
        $this->deletedAt = $chatInfo['deleted_at'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId;
    }

    /**
     * @return string
     */
    public function getCreationTime(): string
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getRemovalTime(): string
    {
        return $this->deletedAt;
    }
}
