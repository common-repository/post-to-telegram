<?php

class TelegramPostMigration1
{
    private function installDbTable($name, $structure)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $name;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name $structure";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    private function installChatsDbTable()
    {
        $name = 'egwp_telegram_post_chats';
        $structure = " (
	          id int NOT NULL AUTO_INCREMENT,
	          name varchar(256) NOT NULL,
	          chat_id varchar(256) NOT NULL,
	          created_at datetime default CURRENT_TIMESTAMP NOT NULL,
	          deleted_at datetime NOT NULL,
	          PRIMARY KEY id (id)
	        );";
        $this->installDbTable($name, $structure);
    }

    private function installActionsDbTable()
    {
        $name = 'egwp_telegram_post_actions';
        $structure = " (
	          id int NOT NULL AUTO_INCREMENT,
	          name varchar(256) NOT NULL,
	          type varchar(256) NOT NULL,
	          is_enabled bool NOT NULL,
	          target_chats text NOT NULL,
	          `condition` text NOT NULL,
	          message text NOT NULL,
	          PRIMARY KEY id (id)
	        );";
        $this->installDbTable($name, $structure);
    }

    private function uninstallTable($name)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $name;
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
    }

    public function up()
    {
        if (!get_option('egwp_telegram_db_ver')) {
            $this->installChatsDbTable();
            $this->installActionsDbTable();
            add_option("egwp_telegram_db_ver", '1.0');
        }
    }

    public function down()
    {
        $this->uninstallTable('egwp_telegram_post_chats');
        $this->uninstallTable('egwp_telegram_post_actions');
        delete_option("egwp_telegram_db_ver");
    }
}