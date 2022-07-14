<?php

namespace tnwpt\helpers;

class AdminColumns
{
  public function register()
  {
    add_filter('manage_posts_columns', [&$this, 'adminColumnsAdd']);
    add_action('manage_posts_custom_column', [&$this, 'adminColumnsView']);
  }

  public function adminColumnsAdd($columns)
  {
    $post_type = get_post_type();

    // THUMBNAIL
    if (in_array($post_type, [])) {
      $num = 1;

      // if ($post_type == 'banner') {
      //   $num = 2;
      // }

      $new_column = ['thumbnail' => ''];
      $columns = array_slice($columns, 0, $num) + $new_column + array_slice($columns, $num);
    }

    // ID
    if (in_array($post_type, [])) {
      $num = 1;
      $new_column = ['pid' => 'ID'];
      $columns = array_slice($columns, 0, $num) + $new_column + array_slice($columns, $num);
    }

    return $columns;
  }

  public function adminColumnsView($column_name)
  {
    if ( !in_array($column_name, ['thumbnail', 'pid']) ) return;

    $post = get_post();

    // THUMBNAIL
    if ($column_name == 'thumbnail') {
      $link = get_edit_post_link();
      $pic = get_template_directory_uri() . '/assets/images/no-image.jpg';

      if (has_post_thumbnail($post)) {
        $pic = get_the_post_thumbnail_url($post, 'medium');
      }

      echo "<a href='{$link}' class='column-thumbnail-link'>
        <div class='column-thumbnail-pic'>
          <img src='{$pic}' alt='Edit {$post->post_title}' class='img' />
        </div>
      </a>";
    }

    // ID
    if ($column_name == 'pid') {
      echo $post->ID;
    }
  }
}

?>
