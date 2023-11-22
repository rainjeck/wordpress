<?php

namespace tnwpt\setup;

class Setup
{
    public function register()
    {
        add_action('after_setup_theme', [&$this, 'themeSetup']);
        add_filter('image_size_names_choose', [&$this, 'themeImageSizeChoose']);
    }

    public function themeSetup()
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
        add_image_size('thumblarge', 500, 500, ['center', 'center']);
        add_image_size('small', 768, 768, false);

        remove_image_size('1536x1536');
        remove_image_size('2048x2048');

        // Menus
        register_nav_menus([
            'place-menu-header' => 'Menu Main',
            'place-menu-footer' => 'Menu Footer'
        ]);
    }

    public function themeImageSizeChoose($size_names)
    {
        $new_sizes = [
            'thumbnail' => __('Thumbnail'),
            'thumblarge' => __('Thumb Large'),
            'small' => __('Small'),
            'medium' => __('Medium'),
            'medium_large' => __('Medium Large'),
            'large' => __('Large'),
            'full' => __( 'Full Size' ),
        ];

        return $new_sizes;
    }
}

?>
