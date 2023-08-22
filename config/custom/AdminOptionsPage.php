<?php

namespace tnwpt\custom;

class AdminOptionsPage
{
  public function register()
  {
    add_action('cmb2_admin_init', [&$this, 'adminOptionsPage']);
    add_action('pre_get_posts', [&$this, 'setupThemeOptionsToQuery']);
  }

  public function adminOptionsPage()
  {
    $mb = new_cmb2_box([
      'id' => 'theme-options',
      'title' => 'Настройки сайта',
      'object_types' => ['options-page'],
      'option_key' => 'tnwpt_app'
    ]);
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
      'attributes' => ['class' => 'large-text']
    ]);


    $g_code = $mb->add_field([
      'type' => 'group',
      'id' => 'g_code',
      'repeatable' => false,
      'options' => ['group_title' => 'Коды', 'closed' => false],
      'desc' => 'Не помещайте код в HEAD. Это тормозит загрузку страниц'
    ]);

    $mb->add_group_field($group_code, [
      'id' => 'head_in',
      'name' => 'HEAD',
      'type' => 'textarea_code'
    ]);

    $mb->add_group_field($g_code, [
      'id' => 'body_start',
      'name' => 'BODY открывающий',
      'type' => 'textarea_code'
    ]);

    $mb->add_group_field($g_code, [
      'id' => 'body_end',
      'name' => 'BODY закрывающий',
      'type' => 'textarea_code'
    ]);
  }

  public function setupThemeOptionsToQuery($query)
  {
    if (is_admin() || !$query->is_main_query()) return;

    // Add theme options to query
    $theme_options = self::getAppOpts('all');

    $theme_options['front_id'] = get_option('page_on_front');

    $query->set('app', $theme_options);
  }

  public static function getAppOpts($key = '', $default = false)
  {
    if (function_exists('cmb2_get_option')) {
      return cmb2_get_option('tnwpt_app', $key, $default);
    }

    $opts = get_option('tnwpt_app', $default);

    $val = $default;

    if ('all' == $key) {
      $val = $opts;
    } elseif (is_array( $opts ) && array_key_exists($key, $opts) && false !== $opts[$key]) {
      $val = $opts[$key];
    }

    return $val;
  }
}

?>
