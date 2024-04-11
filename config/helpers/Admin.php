<?php

namespace tnwpt\helpers;

class Admin
{
    public function register()
    {
        if (is_admin()) {
            // отключим проверку обновлений при любом заходе в админку...
            remove_action('admin_init', '_maybe_update_core');
            remove_action('admin_init', '_maybe_update_plugins');
            remove_action('admin_init', '_maybe_update_themes');

            // отключим проверку обновлений при заходе на специальную страницу в админке...
            remove_action('load-plugins.php', 'wp_update_plugins');
            remove_action('load-themes.php', 'wp_update_themes');

            add_filter('pre_site_transient_browser_'. md5( $_SERVER['HTTP_USER_AGENT'] ), '__return_true');
        }

        add_action('login_enqueue_scripts', [&$this, 'action_login_enqueue_scripts']);
        add_filter('login_headertext', [&$this, 'filter_login_headertext'], 10, 1);
        add_filter('login_headerurl', [&$this, 'filter_login_headerurl'], 10, 1);

        add_action('wp_before_admin_bar_render', [&$this, 'action_wp_before_admin_bar_render']);
        add_action('admin_enqueue_scripts', [&$this, 'action_admin_enqueue_scripts']);
    }

    public function action_login_enqueue_scripts()
    {
        $url = get_template_directory_uri();

        wp_enqueue_style('tnwpt_login', "{$url}/assets/css/admin.min.css", [], null, 'all');
        // wp_enqueue_script('tnwpt-main', "{$url}/assets/js/admin.min.js", [], null, true);
        // wp_enqueue_script('tnwpt-dragondrop', "{$url}/assets/libs/dragon-drop.min.js", [], null, true);
    }

    public function filter_login_headertext($login_header_text)
    {
        return str_replace(['http://', 'https://'], '', get_home_url());
    }

    public function filter_login_headerurl($login_header_url)
    {
        return str_replace(['http:', 'https:'], '', get_home_url());
    }

    public function action_wp_before_admin_bar_render()
    {
        global $wp_admin_bar;

        if (!is_user_logged_in()) return;
        if (!is_user_member_of_blog() && !current_user_can('manage_network')) return;

        $wp_admin_bar->add_menu([
            'id'    => 'site-name',
            'title' => __('Visit Site'),
            'href'  => (is_admin() || ! current_user_can('read')) ? home_url( '/' ) : admin_url(),
            'meta' => ['target' => '_blank', 'title' => 'Open in a new tab']
        ]);

        $wp_admin_bar->remove_node('wp-logo');
        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('view-site');
        $wp_admin_bar->remove_node('user-info');
        $wp_admin_bar->remove_node('edit-profile');

        return $wp_admin_bar;
    }

    public function action_admin_enqueue_scripts()
    {
        $url = get_template_directory_uri();

        wp_enqueue_style('admin-modify', "{$url}/assets/css/admin.min.css", ['cmb2-styles'], null, 'all');

        wp_enqueue_script('admin-main', "{$url}/assets/js/admin.min.js", [], null, true);
    }
}
