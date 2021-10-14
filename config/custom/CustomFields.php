<?php

namespace tnwpt\custom;

class CustomFields
{
  private $prefix = '_tnwpt';
  public static $prefix_static = '_tnwpt';

  public function register()
  {
    add_action( 'cmb2_admin_init', [&$this, 'registerCustomFields'] );
  }

  public function registerCustomFields()
  {

  }

  /*
  private function demo()
  {
    $mb = new_cmb2_box([
      'id' => 'mb-',
      'title' => 'Дополнительные поля',
      'object_types' => ['page', 'term'],
      'taxonomies' => 'tax',
      'show_on' => ['key' => 'id', 'value' => 2]
      'show_on' => ['key' => 'page-template', 'value' => 'template-page-.php']
    ]);

    $mb->add_field([
      'id' => "{$this->prefix}_",
      'name' => '',
      'type' => 'file','file_list',
      'options' => ['url' => false],
      'query_args' => [ 'type' => ['image/jpeg', 'image/png'] ],
      'preview_size' => 'thumbnail'
    ]);

    $mb->add_field([
      'id' => "{$this->prefix}_",
      'name' => '',
      'type' => 'wysiwyg',
      'options' => ['media_buttons' => false, 'textarea_rows' => 5]
    ]);

    $mb->add_field([
      'id' => "{$this->prefix}_",
      'name' => '',
      'type' => 'text','textarea',
      'attributes' => ['class' => 'large-text', 'style' => 'width:99%;', 'rows' => 3]
    ]);

    $group = $mb->add_field([
      'id' => "{$this->prefix}_g_",
      'type' => 'group',
      'options' => [ 'group_title' => 'Item {#}', 'closed' => true ]
    ]);

    $mb->add_group_field($group, [
      'id' => '',
      'name' => '',
      'type' => 'text',
      'attributes' => ['class' => 'large-text']
    ]);
  }
  */
}

?>
