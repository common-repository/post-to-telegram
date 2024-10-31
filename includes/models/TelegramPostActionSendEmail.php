<?php

require_once dirname(__FILE__) . '/TelegramPostAction.php';

class TelegramPostActionSendEmail extends TelegramPostAction
{
    const emailBodyContains = 'Email Body Contains';

    /**
     * @param string $emailBody
     * @param string $str
     *
     * @return false|int
     */
    public function getStringPosition(string $emailBody, string $str)
    {
        return strpos($emailBody, $str);
    }

    /**
     * @param string $emailBody
     * @param string $str
     *
     * @return bool
     */
    public function isEmailBodyContainsStr(string $emailBody, string $str): bool
    {
        if ($this->getStringPosition($emailBody, $str) !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param array $context
     *
     * @return bool
     */
    public function isNeedRun(array $context): bool
    {
        if (!$this->getEnabledStatus()) {
            return false;
        }
        $condition = $this->getCondition();
        if ($condition['type'] === self::emailBodyContains) {
            return $this->isEmailBodyContainsStr($context['message'], $condition['content']);
        }

        return true;
    }
}
