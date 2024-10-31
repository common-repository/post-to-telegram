<?php
require_once dirname(__FILE__) . '/../utils/TelegramPostNotification.php';
require_once dirname(__FILE__) . '/../repositories/TelegramPostChatsRepo.php';


class TelegramPostActionsRepo
{
    const noDataError = 'Cant get data from database';
    const sendEmail = 'send_email';

    public function getTableName()
    {
        global $wpdb;
        return $wpdb->prefix . "egwp_telegram_post_actions";
    }

    /**
     * @return array|Null
     */
    public function getActionsFromDb($sql)
    {
        global $wpdb;
        $actionsData = null;

        try {
            $actionsData = $wpdb->get_results($sql, ARRAY_A);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        if (!$actionsData) {
            TelegramPostNotification::sendError(self::noDataError);
        }

        return $actionsData;
    }

    /**
     * @return array|Null
     */
    public function getAllActions()
    {
        $sql = "SELECT * FROM " . $this->getTableName();

        return $this->getActionsFromDb($sql);
    }

    /**
     * @return array|Null
     */

    public function getActionsSendEmail()
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT * FROM " . $this->getTableName() . "  WHERE type = %s",
            self::sendEmail
        );

        return $this->getActionsFromDb($sql);
    }

    /**
     * @return array|Null
     */
    public function prepareChats()
    {
        $chatsRepo = new TelegramPostChatsRepo();
        return $chatsRepo->getChats();
    }

    /**
     * @param array $actionData
     *
     * @return false|int
     */
    public function addActionToDb(array $actionData)
    {
        global $wpdb;
        return $wpdb->insert($this->getTableName(), $actionData);
    }

    /**
     * @param string $actionName
     * @param string $actionType
     * @return array
     */
    public function prepareActionData(string $actionName, string $actionType): array
    {
        $isEnabled = json_encode(1);
        $targetChats = json_encode([
            'chat_ids' => array_map(function ($chat) {
                return $chat['chat_id'];
            }, $this->prepareChats())
        ]);
        $name = json_encode([
            'name' => $actionName
        ]);
        $condition = json_encode([
            'type' => 'No condition',
            'content' => ''
        ]);
        $message = json_encode([
            'type' => 'Email Body',
            'content' => ''
        ]);
        return [
            'name' => $name,
            'type' => $actionType,
            'is_enabled' => $isEnabled,
            'target_chats' => $targetChats,
            'condition' => $condition,
            'message' => $message
        ];
    }

    /**
     * @param string $actionName
     * @param string $actionType
     * @return false|int
     */
    public function addAction(string $actionName, string $actionType)
    {
        $stringsAdded = false;
        $actionData = $this->prepareActionData($actionName, $actionType);
        try {
            $stringsAdded = $this->addActionToDb($actionData);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        return $stringsAdded;
    }

    /**
     * @param int $id
     *
     * @return false|int
     */
    public function deleteActionFromDb(int $id)
    {
        global $wpdb;

        return $wpdb->delete($this->getTableName(), ['id' => $id]);
    }

    /**
     * @param int $id
     *
     * @return false|int
     */
    public function deleteAction(int $id)
    {
        $stringsRemoved = false;

        try {
            $stringsRemoved = $this->deleteActionFromDb($id);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        return $stringsRemoved;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $id
     *
     * @return false|int
     */
    public function updateAction(string $key, $value, int $id)
    {
        global $wpdb;
        return $wpdb->update($this->getTableName(), [$key => $value], ['id' => $id]);
    }

    /**
     * @param int $status
     * @param int $id
     *
     * @return false|int
     */
    public function setActionStatus(int $status, int $id)
    {
        $updatedValues = false;
        try {
            $updatedValues = $this->updateAction('is_enabled', $status, $id);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        return $updatedValues;
    }

    /**
     * @param int $id
     *
     * @return false|int
     */
    public function enableAction(int $id)
    {
        return $this->setActionStatus(1, $id);
    }

    /**
     * @param int $id
     *
     * @return false|int
     */
    public function disableAction(int $id)
    {
        return $this->setActionStatus(0, $id);
    }

    /**
     * @param string $key
     * @param array $value
     * @param int $id
     *
     * @return false|int
     */
    public function updateOption(string $key, array $value, int $id)
    {
        $updatedValues = false;
        $value = json_encode($value);
        try {
            $updatedValues = $this->updateAction($key, $value, $id);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        return $updatedValues;
    }

    /**
     * @param array $targetChats
     * @param int $id
     *
     * @return false|int
     */
    public function updateTargetChats(array $targetChats, int $id)
    {
        if ($targetChats['chat_ids'] === 'Empty') {
            $targetChats['chat_ids'] = [];
        }

        return $this->updateOption('target_chats', $targetChats, $id);
    }

    /**
     * @param array $condition
     * @param int $id
     *
     * @return false|int
     */
    public function updateCondition(array $condition, int $id)
    {
        return $this->updateOption('condition', $condition, $id);
    }

    /**
     * @param array $message
     * @param int $id
     *
     * @return false|int
     */
    public function updateMessage(array $message, int $id)
    {
        return $this->updateOption('message', $message, $id);
    }

    /**
     * @param array $name
     * @param int $id
     *
     * @return false|int
     */
    public function updateName(array $name, int $id)
    {
        return $this->updateOption('name', $name, $id);
    }
}
