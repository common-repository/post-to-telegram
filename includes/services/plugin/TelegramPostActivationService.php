<?php
require_once dirname(__FILE__) . '/../../repositories/migrations/TelegramPostMigration1.php';

class TelegramPostActivationService
{
    /**
     * @return void
     */
    static function activate()
    {
        flush_rewrite_rules();
        $migration1 = new TelegramPostMigration1();
        $migration1->up();
    }
}
