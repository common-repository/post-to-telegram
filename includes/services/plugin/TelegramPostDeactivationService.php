<?php

class TelegramPostDeactivationService
{
    /**
     * @return void
     */
    static function deactivate()
    {
        flush_rewrite_rules();
    }
}
