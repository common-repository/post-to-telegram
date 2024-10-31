<?php

class TelegramPostAdminService
{
    /**
     * @return void
     */
    public function importAdminPage()
    {
        require_once plugin_dir_path(__FILE__) . '../../../admin/admin.php';
    }

    /**
     * @return void
     */
    public function enqueueAdmin()
    {
        wp_enqueue_script(
            'telegramPostAdminScripts',
            plugin_dir_url(__FILE__) . '../../../build/index.js',
            array('wp-element'), '1.0.0',
            true
        );
    }

    /**
     * @return void
     */
    public function addAdminMenu()
    {
        add_menu_page(
            esc_html__('Post To Telegram Settings Page', 'post-to-telegram'),
            esc_html__('Post To Telegram', 'post-to-telegram'),
            'manage_options',
            'telegram-post-settings',
            [$this, 'importAdminPage'],
            'dashicons-testimonial',
            100
        );
    }

    /**
     * @return string
     */
    public function getSettingsLink(): string
    {
        return '<a href="admin.php?page=telegram-post-settings">' . esc_html__('Settings', 'post-to-telegram') . '</a>';
    }

    /**
     * @param array $links
     *
     * @return array
     */
    public function addSettingsLink(array $links): array
    {
        $links[] = $this->getSettingsLink();

        return $links;
    }

    public function true_load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'post-to-telegram',
            false,
            'post-to-telegram/languages/'
        );
    }

    /**
     * @return void
     */
    public function applyAdminHooks()
    {
        add_action('plugins_loaded', [$this, 'true_load_plugin_textdomain']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdmin']);
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_filter('plugin_action_links_' . "post-to-telegram/post-to-telegram.php", [$this, 'addSettingsLink']);
    }
}
