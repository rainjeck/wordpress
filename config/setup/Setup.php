<?php

namespace tnwpt\setup;

class Setup
{
    public function register()
    {
        add_action('after_setup_theme', [&$this, 'action_after_setup_theme']);
        add_filter('image_size_names_choose', [&$this, 'filter_image_size_names_choose']);
        add_action('wp_enqueue_scripts', [&$this, 'action_wp_enqueue_scripts']);
    }

    public function action_after_setup_theme()
    {
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
        add_theme_support('custom-logo', [
            'height' => 250,
            'width' => 250,
            'flex-width' => true,
            'flex-height' => true,
        ]);

        // Language support
        load_theme_textdomain('tnwpt', get_template_directory() . '/languages');

        // Custom Image Size
        // add_image_size('thumblarge', 500, 500, ['center', 'center']);
        add_image_size('small', 768, 768, false);

        remove_image_size('1536x1536');
        remove_image_size('2048x2048');

        // Menus
        register_nav_menus([
            'place-menu-header' => 'Menu Header',
            'place-menu-footer' => 'Menu Footer'
        ]);
    }

    public function filter_image_size_names_choose($size_names)
    {
        $new_sizes = [
            'thumbnail' => __('Thumbnail'),
            // 'thumblarge' => __('Thumb Large'),
            'small' => __('Small'),
            'medium' => __('Medium'),
            'medium_large' => __('Medium Large'),
            'large' => __('Large'),
            'full' => __( 'Full Size' ),
        ];

        return $new_sizes;
    }

    public function action_wp_enqueue_scripts()
    {
        $url = get_template_directory_uri();

        wp_deregister_style('wp-block-library');
        wp_deregister_style('classic-theme-styles');
        wp_deregister_style('global-styles');

        if (is_user_logged_in()) {
            wp_enqueue_style('main', "{$url}/assets/css/libs.min.css", [], null, 'all');
            wp_enqueue_style('app', "{$url}/assets/css/main.css", ['main'], null, 'all');

            wp_enqueue_script('main', "{$url}/assets/js/libs.js", [], null, true);
            wp_enqueue_script('app', "{$url}/assets/js/main.js", ['main'], null, true);
        }

        if (!is_user_logged_in()) {
            wp_enqueue_style('app', "{$url}/assets/css/bundle.min.css", [], time(), 'all');
            wp_enqueue_script('app', "{$url}/assets/js/bundle.min.js", [], time(), true);
        }

        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script('comment-reply');
        }
    }
}
