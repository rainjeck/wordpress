<?php

namespace tnwpt\custom;

class CustomFields
{
    private $prefix = '';

    public function register()
    {
        add_action('cmb2_admin_init', [&$this, 'registerCustomFields']);
    }

    public function registerCustomFields()
    {
        $this->prefix = $_ENV['CMB'];
    }

  /*
  private function demo()
  {
        $bit = $this->prefix;

        $mb = new_cmb2_box([
            'id' => 'mb-',
            'title' => 'Дополнительные поля',
            'object_types' => ['page', 'term'],
            'taxonomies' => 'tax',
            'show_on' => ['key' => 'id', 'value' => 2]
            'show_on' => ['key' => 'page-template', 'value' => 'template-page-.php']
        ]);

        $mb->add_field([
            'id' => "{$bit}_",
            'name' => '',
            'type' => 'file','file_list',
            'options' => ['url' => false],
            'query_args' => [ 'type' => ['image/jpeg', 'image/png'] ],
            'preview_size' => 'thumbnail'
        ]);

        $mb->add_field([
            'id' => "{$bit}_",
            'name' => '',
            'type' => 'wysiwyg',
            'options' => ['media_buttons' => false, 'textarea_rows' => 5]
        ]);

        $mb->add_field([
            'id' => "{$bit}_",
            'name' => '',
            'type' => 'text','textarea',
            'attributes' => ['class' => 'large-text', 'style' => 'width:99%;', 'rows' => 3]
        ]);

        $mb->add_field([
            'id' => "{$bit}_",
            'name' => '',
            'type' => 'select','radio','radio_inline','checkbox','multicheck','multicheck_inline'
            'options' => [],
            'options_cb' => [&$this, 'functionname'],
        ]);

        $group = $mb->add_field([
            'id' => "{$bit}_g",
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
