<?php
require_once dirname(__FILE__) . '/../../repositories/migrations/TelegramPostMigration1.php';

class TelegramPostUninstallService
{
    /**
     * @return void
     */
    static function uninstall()
    {
        flush_rewrite_rules();
        $migration1 = new TelegramPostMigration1();
        $migration1->down();
    }
}
