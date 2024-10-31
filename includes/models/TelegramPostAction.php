<?php

class TelegramPostAction
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
    private $type;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var array
     */
    private $targetChats;

    /**
     * @var array
     */
    private $condition;

    /**
     * @var array
     */
    private $message;

    /**
     * @param array $actionInfo
     */
    public function __construct(array $actionInfo)
    {
        $this->id = $actionInfo['id'];
        $this->type = $actionInfo['type'];
        $this->isEnabled = $actionInfo['is_enabled'];
        $this->name = (array)json_decode($actionInfo['name']);
        $this->targetChats = (array)json_decode($actionInfo['target_chats']);
        $this->condition = (array)json_decode($actionInfo['condition']);
        $this->message = (array)json_decode($actionInfo['message']);
    }

    /**
     * @return array
     */
    public function getActionData(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'isEnabled' => $this->getEnabledStatus(),
            'targetChats' => $this->getTargetChats(),
            'condition' => $this->getCondition(),
            'message' => $this->getMessage()
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function getEnabledStatus(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return array
     */
    public function getTargetChats(): array
    {
        return $this->targetChats;
    }

    /**
     * @return array
     */
    public function getCondition(): array
    {
        return $this->condition;
    }

    /**
     * @return array
     */
    public function getMessage(): array
    {
        return $this->message;
    }
}
