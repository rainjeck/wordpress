<?php

namespace tnwpt\helpers;

class Filter
{
  public function register()
  {
    add_action( 'navigation_markup_template', [ &$this, 'filterNavigationMarkupTemplate' ] );
    add_action( 'excerpt_more', [ &$this, 'filterExcerptMore' ] );
    add_action( 'the_content', [ &$this, 'filterRemovePWrapperImages' ] );

    add_filter( 'use_block_editor_for_post', '__return_false' );
    add_filter( 'mce_external_plugins', [ &$this, 'mceExternalPlugins' ] );

    // https://www.tiny.cloud/docs/advanced/editor-control-identifiers/#toolbarcontrols
    add_filter( 'mce_buttons', [ &$this, 'enableMoreButtonsRow1' ]);
    add_filter( 'mce_buttons_2', [ &$this, 'enableMoreButtonsRow2' ]);

  }

  public function filterNavigationMarkupTemplate( $template, $class )
  {
    return '<div class="nav-links">%3$s</div>';
  }

  public function filterExcerptMore( $more )
  {
    return '...';
  }

  public function filterRemovePWrapperImages( $content )
  {
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
  }

  public function mceExternalPlugins( $external_plugins )
  {
    // $external_plugins['table'] = get_template_directory_uri() . '/assets/js/tinymce-plugin-table.min.js' ;

    return $external_plugins;
  }

  public function enableMoreButtonsRow1( $buttons_row_1 )
  {
    // print_r($buttons_row_1);

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

  public function enableMoreButtonsRow2( $buttons_row_2 )
  {
    // print_r($buttons_row_2);

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
