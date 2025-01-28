<?php

namespace tnwpt\helpers;

class Admin
{
    public function register()
    {
        add_action('login_enqueue_scripts', [&$this, 'action_login_enqueue_scripts']);
        add_filter('login_headertext', [&$this, 'filter_login_headertext'], 10, 1);
        add_filter('login_headerurl', [&$this, 'filter_login_headerurl'], 10, 1);

        if ( !is_admin() ) return;

        add_action('wp_before_admin_bar_render', [&$this, 'action_wp_before_admin_bar_render']);
        add_action('admin_enqueue_scripts', [&$this, 'action_admin_enqueue_scripts']);

        add_filter('use_block_editor_for_post', '__return_false');

        add_action( 'save_post_page', [&$this, 'action_save_post_page'], 10, 3 );

        add_filter('editor_stylesheets', [&$this, 'filter_editor_stylesheets'], 10, 1);
        add_filter('mce_external_plugins', [&$this, 'filter_mce_external_plugins'], 10, 2);

        // https://www.tiny.cloud/docs/advanced/editor-control-identifiers/#toolbarcontrols
        add_filter('mce_buttons', [&$this, 'filter_mce_buttons'], 10, 2);
        add_filter('mce_buttons_2', [&$this, 'filter_mce_buttons_2'], 10, 2);
    }

    public function action_login_enqueue_scripts()
    {
        $url = get_template_directory_uri();

        wp_enqueue_style('tnwpt_login', "{$url}/assets/css/admin.min.css", [], null, 'all');
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
            'meta' => ['target' => '_blank', 'title' => __('Open in a new tab')]
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

        wp_enqueue_script('admin-main', "{$url}/assets/js/admin.min.js", [], null, ['in_footer' => true, 'strategy'  => 'defer']);

        wp_enqueue_script('admin-libs', "{$url}/assets/js/admin-libs.min.js", ['admin-main'], null, ['in_footer' => true, 'strategy'  => 'defer']);
    }

    public function action_save_post_page($post_id, $post, $update)
    {
        $data = View::getPagesTemplate();

        if (!$data) return;

        update_option('_page_templates', $data, false);
    }

    public function filter_editor_stylesheets($stylesheets)
    {
        $stylesheets[] = get_stylesheet_directory_uri() . '/assets/css/tinymce.min.css';

        return $stylesheets;
    }

    public function filter_mce_external_plugins($external_plugins, $editor_id)
    {
        // $external_plugins['table'] = get_template_directory_uri() . '/assets/libs/tinymce-plugin-table.min.js' ;

        return $external_plugins;
    }

    public function filter_mce_buttons($mce_buttons, $editor_id)
    {
        $mce_buttons = [
            'formatselect',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'bullist',
            'numlist',
            'blockquote',
            'alignleft',
            'aligncenter',
            'alignright',
            'link',
            'wp_more',
            // 'table',
            'wp_adv'
        ];

        return $mce_buttons;
    }

    public function filter_mce_buttons_2($mce_buttons_2, $editor_id)
    {
        $mce_buttons_2 = [
            'pastetext',
            'removeformat',
            'charmap',
            'outdent',
            'indent',
            'undo',
            'redo'
        ];

        return $mce_buttons_2;
    }
}
