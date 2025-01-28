<?php

namespace tnwpt\custom;

use tnwpt\helpers\View;

class CustomFields
{
    private $prefix = '';
    private $front_id = 0;

    public function register()
    {
        add_action('cmb2_admin_init', [&$this, 'action_cmb2_admin_init']);
    }

    public function action_cmb2_admin_init()
    {
        $this->prefix = $_ENV['CMB'];

        $this->front_id = get_option('page_on_front');

        // $this->pageFront();
    }

    public function pageFront()
    {
        if ( !View::checkArray($_GET,'post',$this->front_id) ) return;

        $bit = $this->prefix;

        $mb = new_cmb2_box([
            'id' => 'mb-page-front',
            'title' => 'Дополнительные поля',
            'object_types' => ['page'],
            'show_on' => [ 'key' => 'id', 'value' => $this->front_id ]
        ]);
    }

    public function sanitization_field($value, $field_args, $field)
    {
        $sanitized_value = strip_tags($value, ['br','strong','b','em','i','iframe']);

        return $sanitized_value;
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
            'query_args' => [ 'type' => ['image/jpeg','image/png','image/webp'] ],
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
            'attributes' => ['class' => 'large-text', 'style' => 'width:99%;', 'rows' => 3],
            'sanitization_cb' => [&$this, 'sanitization_field'],
        ]);

        $mb->add_field([
            'id' => "{$bit}_",
            'name' => '',
            'type' => 'select','radio','radio_inline','checkbox','multicheck','multicheck_inline'
            'options' => [],
            'options_cb' => [&$this, 'select_post_order'],
            'attributes' => ['class' => 'bDragDropList js-drag-drop'],
            'desc' => 'Drag & Drop. Не отмеченные не показываются',
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

    public function select_post_order($field)
    {
        $ids = $field->value;

        $data = View::getPostsOrder($ids, 'post_type');

        return $data;
    }
    */
}
