<?php

namespace tnwpt\helpers;

class Filter
{
    public function register()
    {
        add_action('navigation_markup_template', [&$this, 'action_navigation_markup_template'], 10, 2);
        add_action('excerpt_more', [&$this, 'action_excerpt_more'], 10, 1);
        add_action('the_content', [&$this, 'action_the_content'], 10, 1);

        // Microdata
        add_filter('nav_menu_item_args', [&$this, 'filter_nav_menu_item_args_schemaorg'], 10, 3);
        add_filter('nav_menu_link_attributes', [$this, 'filter_nav_menu_link_attributes_schemaorg'], 10, 4);
        add_filter('wp_nav_menu_items', [&$this, 'filter_wp_nav_menu_items_schemaorg'], 10, 2);
    }

    public function action_navigation_markup_template($template, $css_class)
    {
        return '<div class="nav-links">%3$s</div>';
    }

    public function action_excerpt_more($more_string)
    {
        return '...';
    }

    public function action_the_content($content)
    {
        $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
        $content = View::getRelativeUrl($content);

        return $content;
    }

    public function filter_nav_menu_item_args_schemaorg($args, $menu_item, $depth)
    {
        if ($args->theme_location !== 'place-menu-header') return $args;

        $args->after = "<meta itemprop='name' content='{$menu_item->title}'>";

        return $args;
    }

    public function filter_nav_menu_link_attributes_schemaorg($atts, $menu_item, $args, $depth)
    {
        $atts['href'] = View::getRelativeUrl($atts['href']);

        if ($args->theme_location !== 'place-menu-header') return $atts;

        $atts['itemprop'] = 'url';

        return $atts;
    }

    public function filter_wp_nav_menu_items_schemaorg($items, $args)
    {
        if ($args->theme_location !== 'place-menu-header') return $items;

        $items = preg_replace('/<li /', '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList" ', $items);

        return $items;
    }
}
