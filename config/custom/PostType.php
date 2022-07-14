<?php

namespace tnwpt\custom;

class PostType
{
  public function register()
  {
    add_action('init', [&$this, 'registerPostTypes']);
    add_action('dashboard_glance_items' , [&$this, 'consoleOnShowMetabox']);

  }

  public function registerPostTypes()
  {

  }

  /*
  private function registerPostType()
  {
    register_post_type('', [
      'label'  => '',
      'labels' => [
        'name' => '',
        'singular_name' => ''
      ],
      'description' => '',
      'public' => true,
      'show_in_menu' => true,
      'menu_position' => 25,
      'menu_icon' => 'dashicons-format-aside',
      'hierarchical' => false,
      'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
      'taxonomies' => [],
      'has_archive' => false,
      'rewrite' => true,
      'query_var' => true
    ]);
  }

  private function registerTaxonomy()
  {
    register_taxonomy('taxonomy_name', 'post_types', [
      'label' => '',
      'labels' => [
        'name' => '',
        'singular_name' => ''
      ],
      'description' => '',
      'public' => true,
      'show_in_rest' => null,
      'rest_base' => null, // $taxonomy
      'hierarchical' => true,
      'update_count_callback' => '',
      'rewrite' => true,
      'capabilities' => array(),
      'meta_box_cb' => null,
      'show_admin_column' => true,
      '_builtin' => false,
    ]);
  }
  */


  public function consoleOnShowMetabox( $items )
  {
    if ( ! current_user_can('edit_posts') ) return $items; // выходим

    // типы записей
    $args = array( 'public' => true, '_builtin' => false );

    $post_types = get_post_types( $args, 'object', 'and' );
    foreach ( $post_types as $post_type ) {
      $num_posts = wp_count_posts( $post_type->name );
      $num = number_format_i18n( $num_posts->publish );
      $text = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
      $items[] = "<a href=\"edit.php?post_type=$post_type->name\">$text - $num</a>";
    }

    // таксономии
    // $taxonomies = get_taxonomies( $args, 'object', 'and' );
    // foreach( $taxonomies as $taxonomy ) {
    //   $num_terms = wp_count_terms( $taxonomy->name );
    //   $num = number_format_i18n( $num_terms );
    //   $text = _n( $taxonomy->labels->singular_name, $taxonomy->labels->name , intval( $num_terms ) );
    //   $items[] = "<a href='edit-tags.php?taxonomy=$taxonomy->name'>$text - $num</a>";
    // }

    // пользователи
    // global $wpdb;
    // $num  = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
    // $text = _n( 'User', 'Users', $num );
    // $items[] = "<a href='users.php'>$num $text</a>";

    return $items;
  }
}

?>
