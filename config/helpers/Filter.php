<?php

namespace tnwpt\helpers;

class Filter
{
    public function register()
    {
        add_action('navigation_markup_template', [&$this, 'filterNavigationMarkupTemplate']);
        add_action('excerpt_more', [&$this, 'filterExcerptMore']);
        add_action('the_content', [&$this, 'filterTheContent']);

        // Microdata
        add_filter( 'nav_menu_item_args', [&$this, 'filterNavMenuItemArgsMicrodata'], 10, 3 );
        add_filter( 'nav_menu_link_attributes', [$this, 'filterNavMenuLinkAttributesMicrodata'], 10, 4 );
        add_filter( 'wp_nav_menu_items', [&$this, 'filterNavMenuItemsMicrodata'], 10, 2 );

        if (is_admin()) {
            add_action( 'save_post_page', [&$this, 'actionSavePostPage'], 10, 3 );

            add_filter('use_block_editor_for_post', '__return_false');

            add_filter('editor_stylesheets', [&$this, 'filterEditorStylesheets']);
            add_filter('mce_external_plugins', [&$this, 'filterMceExternalPlugins']);

            // https://www.tiny.cloud/docs/advanced/editor-control-identifiers/#toolbarcontrols
            add_filter('mce_buttons', [&$this, 'filterMceButtons']);
            add_filter('mce_buttons_2', [&$this, 'filterMceButtons2']);
        }
    }

    public function filterNavigationMarkupTemplate($template, $class)
    {
        return '<div class="nav-links">%3$s</div>';
    }

    public function filterExcerptMore($more)
    {
        return '...';
    }

    public function filterTheContent($content)
    {
        return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    }

    public function filterNavMenuItemArgsMicrodata($args, $menu_item, $depth)
    {
        if ($args->theme_location !== 'place-menu-header') return $args;

        $args->after = "<meta itemprop='name' content='{$menu_item->title}'>";

        return $args;
    }

    public function filterNavMenuLinkAttributesMicrodata($atts, $menu_item, $args, $depth)
    {
        if ($args->theme_location !== 'place-menu-header') return $atts;

        $atts['itemprop'] = 'url';

        return $atts;
    }

    public function filterNavMenuItemsMicrodata($items, $args)
    {
        if ($args->theme_location !== 'place-menu-header') return $items;

        $items = preg_replace('/<li /', '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList" ', $items);

        return $items;
    }

    public function actionSavePostPage($post_id, $post, $update)
    {
        $data = View::getPagesTemplate();

        if (!$data) return;

        update_option('_page_templates', $data, false);
    }

    public function filterEditorStylesheets($stylesheets)
    {
        $stylesheets[] = get_stylesheet_directory_uri() . '/assets/css/tinymce.min.css';

        return $stylesheets;
    }

    public function filterMceExternalPlugins($external_plugins)
    {
        // $external_plugins['table'] = get_template_directory_uri() . '/assets/js/tinymce-plugin-table.min.js' ;

        return $external_plugins;
    }

    public function filterMceButtons($buttons_row_1)
    {
        $buttons_row_1 = [
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

        return $buttons_row_1;
    }

    public function filterMceButtons2($buttons_row_2)
    {
        $buttons_row_2 = [
            'pastetext',
            'removeformat',
            'charmap',
            'outdent',
            'indent',
            'undo',
            'redo'
        ];

        return $buttons_row_2;
    }
}

?>
