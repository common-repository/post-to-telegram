<?php

require_once dirname(__FILE__) . '/plugin/TelegramPostAdminService.php';
require_once dirname(__FILE__) . '/telegram/TelegramPostClient.php';
require_once dirname(__FILE__) . '/../models/TelegramPostActionSendEmail.php';
require_once dirname(__FILE__) . '/../repositories/TelegramPostActionsRepo.php';
require_once dirname(__FILE__) . '/../repositories/TelegramPostChatsRepo.php';
require_once dirname(__FILE__) . '/../controllers/TelegramPostChatsController.php';
require_once dirname(__FILE__) . '/../controllers/TelegramPostActionsController.php';

class TelegramPostService
{
    const customMessage = 'Custom Message';
    const email = "Email";

    /**
     * @var TelegramPostAdminService
     */
    private $adminPage;

    /**
     * @var TelegramPostChatsRepo
     */
    private $chatsRepo;

    /**
     * @var TelegramPostActionsRepo
     */
    private $actionsRepo;

    /**
     * @var TelegramPostChatsController
     */
    private $chatsController;

    /**
     * @var TelegramPostActionsController
     */
    private $actionsController;

    public function __construct()
    {
        $this->adminPage = new TelegramPostAdminService();
        $this->chatsRepo = new TelegramPostChatsRepo();
        $this->actionsRepo = new TelegramPostActionsRepo();
        $this->chatsController = new TelegramPostChatsController($this->chatsRepo);
        $this->actionsController = new TelegramPostActionsController($this->actionsRepo);
    }

    /**
     * @return void
     */
    private function initAdminPanel()
    {
        $this->adminPage->applyAdminHooks();
    }

    /**
     * @return array|null
     */
    public function getActionsSendEmailModels()
    {
        $actionsSendEmailData = $this->actionsRepo->getActionsSendEmail();
        if ($actionsSendEmailData === null) {
            return null;
        }

        $actionsSendEmailModels = [];
        foreach ($actionsSendEmailData as $actionData) {
            $actionsSendEmailModels[] = new TelegramPostActionSendEmail($actionData);
        }

        return $actionsSendEmailModels;
    }

    /**
     * @param string $message
     * @param array $targetChats
     * @param string $type
     * @param string $subject
     * @return void
     */
    public function sendTelegramMessage(string $message, array $targetChats, string $type, string $subject)
    {
        TelegramPostClient::sendTelegramMessage($message, $targetChats, $type, $subject);
    }

    /**
     * @param TelegramPostActionSendEmail $actionSendEmail
     * @param array $mailData
     */
    public function sendEmail(TelegramPostActionSendEmail $actionSendEmail, array $mailData)
    {
        if ($actionSendEmail->isNeedRun($mailData)) {
            $message = $actionSendEmail->getMessage();
            $targetChats = $actionSendEmail->getTargetChats();
            $message = $message['type'] === self::customMessage ? $message['content'] : $mailData['message'];
            if (gettype($targetChats['chat_ids']) === 'array' && gettype($message) == 'string') {
                $this->sendTelegramMessage($message, $targetChats['chat_ids'], self::email, $mailData['subject']);
            }
        }
    }

    /**
     * @param array $mailData
     *
     * @return array
     */
    public function sendAllEmails(array $mailData): array
    {
        $actionsSendEmail = $this->getActionsSendEmailModels();
        if ($actionsSendEmail !== null) {
            foreach ($actionsSendEmail as $action) {
                $this->sendEmail($action, $mailData);
            }
        }

        return $mailData;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->initAdminPanel();
        add_filter('wp_mail', [$this, 'sendAllEmails']);

        $this->chatsController->addActions();
        $this->actionsController->addActions();
    }
}
