<?php

/*
Plugin Name: Post To Telegram
Plugin URI: https://egsite.ru/wp-telegram-post/
Description: Add Telegram integration to your site
Version: 1.0.3
Author: Egsite
Author URI: https://egsite.ru/
License: GPLv3
Text Domain: post-to-telegram
*/

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class TelegramPostClass {
	/**
	 * @return void
	 */
	public function activation() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/services/plugin/TelegramPostActivationService.php';
		TelegramPostActivationService::activate();
	}

	/**
	 * @return void
	 */
	public function deactivation() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/services/plugin/TelegramPostDeactivationService.php';
		TelegramPostDeactivationService::deactivate();
	}

	/**
	 * @return void
	 */
	public function run() {
		require_once plugin_dir_path( __FILE__ ) . '/includes/services/TelegramPostService.php';
		$telegramPostService = new TelegramPostService();
		$telegramPostService->run();
	}
}


$telegramPost = new TelegramPostClass();
function uninstallTelegramPost() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/services/plugin/TelegramPostUninstallService.php';
    TelegramPostUninstallService::uninstall();
}

register_activation_hook( __FILE__, [ $telegramPost, 'activation' ] );
register_deactivation_hook( __FILE__, [ $telegramPost, 'deactivation' ] );
register_uninstall_hook( __FILE__, 'uninstallTelegramPost');

$telegramPost->run();
