<?php
require_once dirname(__FILE__) . '/../utils/TelegramPostNotification.php';

class TelegramPostChatsRepo
{
    const noDataError = 'Cant get data from database';

    public function getTableName()
    {
        global $wpdb;
        return $wpdb->prefix . "egwp_telegram_post_chats";
    }

    /**
     * @return array|Null
     */
    public function getChats()
    {
        global $wpdb;
        $chatsData = null;

        try {
            $chatsData = $wpdb->get_results("SELECT * FROM " . $this->getTableName(), ARRAY_A);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        if (!$chatsData) {
            TelegramPostNotification::sendError(self::noDataError);
        }

        return $chatsData;
    }

    /**
     * @param array $chatData
     *
     * @return false|int
     */
    public function addChatToDb(array $chatData)
    {
        global $wpdb;

        return $wpdb->insert($this->getTableName(), $chatData);
    }

    /**
     * @param array $chatData
     *
     * @return false|int
     */
    public function addChat(array $chatData)
    {
        $stringsAdded = false;
        try {
            $stringsAdded = $this->addChatToDb($chatData);
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
    public function deleteChatFromDb(int $id)
    {
        global $wpdb;

        return $wpdb->delete($this->getTableName(), ['id' => $id]);
    }

    /**
     * @param int $id
     *
     * @return false|int
     */
    public function deleteChat(int $id)
    {
        $stringsRemoved = false;

        try {
            $stringsRemoved = $this->deleteChatFromDb($id);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        return $stringsRemoved;
    }
}
