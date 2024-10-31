<?php
require_once dirname(__FILE__) . '/../../utils/TelegramPostNotification.php';

class TelegramPostClient
{
    /**
     * @param string $message
     * @param array $targetChats
     * @param string $type
     * @param string $subject
     * @return array|WP_Error|null
     */
    static function sendTelegramMessage(string $message, array $targetChats, string $type = '', string $subject = '')
    {
        $body = [
            "chat_ids" => $targetChats,
            "body" => $message,
            "type" => $type,
            "subject" => $subject
		];
        $res = null;
        try {
            $res = wp_remote_post('https://api.egwp.ru/telegram_post', [
                'timeout' => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => ['Referer' => get_site_url()],
                'body' => $body,
                'cookies' => array()
            ]);
            $res['body'] = json_decode($res['body']);
        } catch (Throwable $err) {
            TelegramPostNotification::sendError($err);
        }

        return $res;
    }
}
