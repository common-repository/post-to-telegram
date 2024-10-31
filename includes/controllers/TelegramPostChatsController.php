<?php
require_once dirname(__FILE__) . '/../repositories/TelegramPostChatsRepo.php';
require_once dirname(__FILE__) . '/../services/telegram/TelegramPostClient.php';

class TelegramPostChatsController
{
    const badRequestError = '400';
    const ok = 'ok';

    /**
     * @var TelegramPostChatsRepo
     */
    private $chatsRepo;

    /**
     * @param TelegramPostChatsRepo $chatsRepo
     */
    public function __construct(TelegramPostChatsRepo $chatsRepo)
    {
        $this->chatsRepo = $chatsRepo;
    }

    /**
     * @return void
     */
    public function sendTestMessage()
    {
        $done = false;
        if (!empty($_POST['telegram_post_test_data'])) {
            $message = sanitize_text_field($_POST['telegram_post_test_data']['message']);
            $chatId = sanitize_text_field($_POST['telegram_post_test_data']['chatId']);
            $done = TelegramPostClient::sendTelegramMessage($message, [$chatId]);
        }
        $res = $done ?: self::badRequestError;
        echo json_encode($res);
        wp_die();
    }

    /**
     * @return void
     */
    public function addChat()
    {
        $chatData = empty($_POST['telegram_post_chat_data'])
            ? null
            : array_map('sanitize_text_field', $_POST['telegram_post_chat_data']);
        $done = $chatData ? $this->chatsRepo->addChat($chatData) : false;
        $res = $done ? self::ok : self::badRequestError;
        echo json_encode(esc_html($res));
        wp_die();
    }

    /**
     * @return void
     */
    public function deleteChat()
    {
        $id = empty($_POST['telegram_post_chat_id']) ? null : sanitize_text_field($_POST['telegram_post_chat_id']);
        $done = $id ? $this->chatsRepo->deleteChat((int)$id) : false;
        $res = $done ? self::ok : self::badRequestError;
        echo json_encode(esc_html($res));
        wp_die();
    }

    /**
     * @return void
     */
    public function getChats()
    {
        $chats = $this->chatsRepo->getChats();
        $chats = array_map(function ($chat) {
            return array_map('esc_html', $chat);
        }, $chats);
        echo json_encode($chats);
        wp_die();
    }

    /**
     * @return void
     */
    public function addActions()
    {
        add_action('wp_ajax_telegram_post_send_test_message', [$this, 'sendTestMessage']);
        add_action('wp_ajax_telegram_post_add_chat', [$this, 'addChat']);
        add_action('wp_ajax_telegram_post_delete_chat', [$this, 'deleteChat']);
        add_action('wp_ajax_telegram_post_get_chats', [$this, 'getChats']);
    }
}
