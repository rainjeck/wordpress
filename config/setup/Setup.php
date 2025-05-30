<?php

namespace tnwpt\setup;

use tnwpt\helpers\View;

class Setup
{
    public function register()
    {
        add_action('after_setup_theme', [&$this, 'action_after_setup_theme']);

        add_filter('image_size_names_choose', [&$this, 'filter_image_size_names_choose']);

        add_action('wp_enqueue_scripts', [&$this, 'action_wp_enqueue_scripts']);

        add_filter('style_loader_src', [&$this, 'filter_enqueue_loader_src'], 10, 2 );
        add_filter('script_loader_src', [&$this, 'filter_enqueue_loader_src'], 10, 2 );
        add_filter('style_loader_tag', [$this, 'filter_style_loader_tag'], 10, 4 );
    }

    public function action_after_setup_theme()
    {
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
        add_theme_support('custom-logo', [
            'height' => 512,
            'width' => 512,
            'flex-width' => true,
            'flex-height' => true,
        ]);

        // Language support
        load_theme_textdomain('tnwpt', get_template_directory() . '/languages');

        // Custom Image Size
        // add_image_size('thumblarge', 500, 500, ['center', 'center']);
        // add_image_size('small', 768, 768, false);

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
            // 'medium_large' => __('Medium Large'),
            'large' => __('Large'),
            // 'full' => __( 'Full Size' ),
        ];

        return $new_sizes;
    }

    public function action_wp_enqueue_scripts()
    {
        $url = get_template_directory_uri();

        wp_deregister_style('wp-block-library');
        wp_deregister_style('classic-theme-styles');
        wp_deregister_style('global-styles');

        $ver = null;

        if ( is_user_logged_in() ) {
            $ver = time();
        }

        wp_enqueue_style('theme-app', "{$url}/assets/css/main.css", [], $ver, '');
        wp_enqueue_script('theme-app', "{$url}/assets/js/main.js", [], $ver, ['in_footer' => true, 'strategy'  => 'defer']);
    }

    public function filter_enqueue_loader_src($src, $handle)
    {
        return View::getRelativeUrl($src);
    }

    public function filter_style_loader_tag($tag, $handle, $href, $media)
    {
        $new_tag = preg_replace('/(media=\'.*\')/','',$tag);
        return $new_tag;
    }
}
