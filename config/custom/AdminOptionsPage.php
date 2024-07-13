<?php

namespace tnwpt\custom;

use tnwpt\helpers\View;

class AdminOptionsPage
{
    public function register()
    {
        add_action('cmb2_admin_init', [&$this, 'action_cmb2_admin_init']);
        add_action('pre_get_posts', [&$this, 'action_pre_get_posts']);
    }

    public function action_cmb2_admin_init()
    {
        $mb = new_cmb2_box([
            'id' => 'theme-options',
            'title' => 'Настройки сайта',
            'object_types' => ['options-page'],
            'option_key' => $_ENV['APPOPTSKEY']
        ]);

        // OPTS
            $mb->add_field([
                'id' => 'setup_title',
                'name' => 'Основное',
                'type' => 'title'
            ]);
            $mb->add_field([
                'id' => 'phone',
                'name' => 'Телефон на сайте',
                'type' => 'text',
                'attributes' => ['class' => 'large-text']
            ]);
            $mb->add_field([
                'id' => 'email',
                'name' => 'E-Mail на сайте',
                'type' => 'text_email',
                'attributes' => ['class' => 'large-text']
            ]);
            $mb->add_field([
                'id' => 'email_order',
                'name' => 'E-Mail для заявок',
                'type' => 'text_email',
                'attributes' => ['class' => 'large-text'],
                'repeatable' => true,
            ]);
        //

        // CODE
            $mb->add_field([
                'id' => 'codes_title',
                'name' => 'Коды',
                'type' => 'title',
                'desc' => 'Не помещайте код в HEAD. Это тормозит загрузку страниц'
            ]);
            $mb->add_field([
                'id' => 'head',
                'name' => 'HEAD',
                'type' => 'textarea_code'
            ]);
            $mb->add_field([
                'id' => 'bodystart',
                'name' => 'BODY открывающий',
                'type' => 'textarea_code'
            ]);
            $mb->add_field([
                'id' => 'bodyend',
                'name' => 'BODY закрывающий',
                'type' => 'textarea_code'
            ]);
        //
    }

    /**
     * Add theme options to query
     */
    public function action_pre_get_posts($query)
    {
        if ( is_admin() || !$query->is_main_query() ) return;

        $theme_options = View::getOpt('all');

        $theme_options['front_id'] = get_option('page_on_front');

        $query->set('app', $theme_options);
    }
}
