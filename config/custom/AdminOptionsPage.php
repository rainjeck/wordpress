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
      'title' => 'Theme Fields',
      'object_types' => ['options-page'],
      'option_key' => 'tnwpt_app'
    ]);

    $mb->add_field([
      'id' => 'setup_title',
      'name' => 'Setup',
      'type' => 'title'
    ]);

    $mb->add_field([
      'id' => 'phone',
      'name' => 'Site Phone',
      'type' => 'text',
      'attributes' => ['class' => 'large-text']
    ]);

    $mb->add_field([
      'id' => 'email',
      'name' => 'Site E-Mail',
      'type' => 'text_email',
      'attributes' => ['class' => 'large-text']
    ]);

    $mb->add_field([
      'id' => 'email_order',
      'name' => 'E-Mail for forms',
      'type' => 'text_email',
      'attributes' => ['class' => 'large-text']
    ]);


    $g_code = $mb->add_field([
      'type' => 'group',
      'id' => 'g_code',
      'repeatable' => false,
      'options' => ['group_title' => 'Codes', 'closed' => false],
      'desc' => "Don't put any code in the HEAD tag. It slows loading"
    ]);

    // $mb->add_group_field($group_code, [
    //   'id' => 'head_in',
    //   'name' => 'Code in HEAD tag',
    //   'type' => 'textarea_code'
    // ]);

    $mb->add_group_field($g_code, [
      'id' => 'body_start',
      'name' => 'Start BODY tag',
      'type' => 'textarea_code'
    ]);

    $mb->add_group_field($g_code, [
      'id' => 'body_end',
      'name' => 'End BODY tag',
      'type' => 'textarea_code'
    ]);
  }

  public function setupThemeOptionsToQuery($query)
  {
    if (is_admin() || !$query->is_main_query()) return;

    // Add theme options to query
    $theme_options = self::getAppOpts('all');
    $query->set('theme_options', $theme_options);
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
