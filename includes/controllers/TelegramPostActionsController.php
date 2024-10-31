<?php
require_once dirname(__FILE__) . '/../models/TelegramPostAction.php';

class TelegramPostActionsController
{
    const badRequestError = '400';

    const ok = 'ok';

    /**
     * @var TelegramPostChatsRepo
     */
    private $actionsRepo;

    /**
     * @param TelegramPostActionsRepo $actionsRepo
     */
    public function __construct(TelegramPostActionsRepo $actionsRepo)
    {
        $this->actionsRepo = $actionsRepo;
    }

    /**
     * @return void
     */
    public function getActions()
    {
        $actionsSendEmailData = $this->actionsRepo->getAllActions();
        $actionsSendEmail = [];
        foreach ($actionsSendEmailData as $actionData) {
            $actionModel = new TelegramPostAction($actionData);
            $actionsSendEmail[] = $actionModel->getActionData();
        }

        echo json_encode($actionsSendEmail);
        wp_die();
    }

    /**
     * @return void
     */
    public function addAction()
    {
        $actionData = empty($_POST['telegram_post_action_data'])
            ? null
            : array_map('sanitize_text_field', $_POST['telegram_post_action_data']);
        $done = $actionData ? $this->actionsRepo->addAction($actionData['name'], $actionData['type']) : false;
        $res = $done ? self::ok : self::badRequestError;
        echo json_encode($res);
        wp_die();
    }

    /**
     * @return void
     */
    public function deleteAction()
    {
        $id = empty($_POST['telegram_post_action_id'])
            ? null
            : sanitize_text_field($_POST['telegram_post_action_id']);
        $done = $id ? $this->actionsRepo->deleteAction((int)$id) : false;
        $res = $done ? self::ok : self::badRequestError;
        echo json_encode($res);
        wp_die();
    }

    /**
     * @return void
     */
    public function enableAction()
    {
        $actionId = empty($_POST['telegram_post_action_id'])
            ? null
            : sanitize_text_field($_POST['telegram_post_action_id']);
        $isDisabled = $this->actionsRepo->enableAction($actionId);
        $res = $isDisabled ? self::ok : self::badRequestError;
        echo json_encode($res);
        wp_die();
    }

    /**
     * @return void
     */
    public function disableAction()
    {
        $actionId = empty($_POST['telegram_post_action_id'])
            ? null
            : sanitize_text_field($_POST['telegram_post_action_id']);
        $isDisabled = $this->actionsRepo->disableAction($actionId);
        $res = $isDisabled ? self::ok : self::badRequestError;
        echo json_encode($res);
        wp_die();
    }

    /**
     * @param $name
     * @param $callback
     *
     * @return void
     */
    private function updateOption($name, $callback)
    {
        $name = 'telegram_post_action_' . $name;
        $done = false;
        if (!empty($_POST[$name])) {
            $content = array_map('sanitize_text_field', (array)$_POST[$name]['content']);
            $id = sanitize_text_field((int)$_POST[$name]['id']);
            $done = $callback($content, $id);
        }

        $res = $done ? self::ok : self::badRequestError;
        echo json_encode($res);
        wp_die();
    }

    /**
     * @return void
     */
    public function updateTargetChats()
    {
        $var = 'telegram_post_action_target_chats';
        $done = false;
        if (!empty($_POST[$var])) {
            $targetChats = [];
            $targetChats['chat_ids'] = array_map(
                'sanitize_text_field',
                (array)$_POST[$var]['content']['chat_ids']
            );
            $id = sanitize_text_field((int)$_POST[$var]['id']);
            $done = $this->actionsRepo->updateTargetChats($targetChats, $id);
        }

        $res = $done ? self::ok : self::badRequestError;
        echo json_encode($res);
        wp_die();
    }

    /**
     * @return void
     */
    public function updateCondition()
    {
        $this->updateOption('condition', [$this->actionsRepo, 'updateCondition']);
    }

    /**
     * @return void
     */
    public function updateMessage()
    {
        $this->updateOption('message', [$this->actionsRepo, 'updateMessage']);
    }

    /**
     * @return void
     */
    public function updateName()
    {
        $this->updateOption('name', [$this->actionsRepo, 'updateName']);
    }

    /**
     * @return void
     */
    public function addActions()
    {
        add_action('wp_ajax_telegram_post_get_actions', [$this, 'getActions']);
        add_action('wp_ajax_telegram_post_add_action', [$this, 'addAction']);
        add_action('wp_ajax_telegram_post_delete_action', [$this, 'deleteAction']);
        add_action('wp_ajax_telegram_post_enable_action', [$this, 'enableAction']);
        add_action('wp_ajax_telegram_post_disable_action', [$this, 'disableAction']);
        add_action('wp_ajax_telegram_post_update_target_chats', [$this, 'updateTargetChats']);
        add_action('wp_ajax_telegram_post_update_condition', [$this, 'updateCondition']);
        add_action('wp_ajax_telegram_post_update_message', [$this, 'updateMessage']);
        add_action('wp_ajax_telegram_post_update_name', [$this, 'updateName']);
    }
}
