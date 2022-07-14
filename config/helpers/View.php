<?php

namespace tnwpt\helpers;

use tnwpt\custom\CustomFields;

class View
{
  public static $url = '';

  public function register()
  {
    self::$url = get_template_directory_uri();

    return;
  }

  public static function debug($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
  }

  public static function getPhoneNumber($number)
  {
    if ( !$number ) return;

    return preg_replace('/[^0-9]/', '', $number);
  }

  public static function getPhoneLink($number, $className = '')
  {
    if ( !$number ) return;

    $clearNum = self::getPhoneNumber($number);
    return '<a href="tel:+'. $clearNum .'" class="'. $className .'">'. $number .'</a>';
  }

  public static function getEmailLink($email, $className = '')
  {
    return '<a href="mailto:'. $email .'" class="'. $className .'">'. $email .'</a>';
  }

  public static function getCustomLogo($bg = false)
  {
    $logo_id = get_theme_mod('custom_logo');

    if ($logo_id) {
      $home_url = home_url();
      $url = wp_get_attachment_image_url($logo_id, 'small');
      $name = get_bloginfo('name');
      $desc = get_bloginfo('description');

      $style = '';

      if ($bg) {
        $style = " style='background-image: url({$url})'";
      }

      $html = "<a href='{$home_url}' class='customLogo'{$bg}>
        <img src='{$url}' title='{$desc}' alt='{$name}' class='customLogo-img'/>
      </a>";

      return $html;
    }
  }

  public static function getImg($filename, $folder)
  {
    $url = self::$url;
    return "{$url}/assets/{$folder}/{$filename}";
  }

  public static function getSvg($icon_id, $className = '')
  {
    if (!$icon_id) return;

    $addClass = ( $className ) ? ' '.$className : '';

    $url = self::$url . '/assets/icons/sprite.svg#';

    return "<svg class='ico{$addClass}'><use xlink:href='{$url}{$icon_id}'></use></svg>";
  }

  public static function getSvgColor($icon_id, $className = '')
  {
    if (!$icon_id) return;

    $addClass = ( $className ) ? ' '.$className : '';

    $url = self::$url . '/assets/icons/sprite-color.svg#';

    return "<svg class='ico{$addClass}'><use xlink:href='{$url}{$icon_id}'></use></svg>";
  }

  public static function getPluralForm($number, $after)
  {
    /*
    plural_form($number, [__('вариант', 'theme'), __('варианта', 'theme'), __('вариантов', 'theme')]);
    */

    $cases = array (2, 0, 1, 1, 1, 2);

    return $after[($number % 100 > 4 && $number % 100 < 20) ? 2: $cases[min($number % 10, 5)]];
  }

  public static function wpautop($text)
  {
    if ( !$text ) return;

    $content = wpautop( $text );
    $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);

    return $content;
  }

  public static function getPostIdByTemplate($template_file)
  {
    if (!$template_file) return;

    global $wpdb;

    $sql = "
      SELECT post_id
      FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = '{$template_file}'
    ";

    $q = $wpdb->get_var($sql);

    if ( $q ) {
      return (int)$q;
    }

    return;
  }

  public static function getPostMeta($post_id, $keys = [])
  {
    if ( !$post_id || !$keys ) return;

    $prefix = CustomFields::$prefix_static;

    $arr = [];

    foreach ($keys as $key) {
      $meta = get_post_meta($post_id, "{$prefix}_{$key}", 1);

      if ( $meta ) {
        $arr[$key] = $meta;
      } else {
        $arr[$key] = false;
      }
    }

    return $arr;
  }

  public static function checkMeta($metas, $key, $return_type = '', $array_key = -1, $return_array_key = false)
  {
    $result = $return_type;

    if ($array_key < 0) {
      // simple check
      $result = (isset($metas[$key]) && !empty($metas[$key])) ? $metas[$key] : $return_type;
    } else {
      // if array (group)
      if (!$return_array_key) {
        // return full group
        $result = (isset($metas[$key]) && !empty($metas[$key]) && isset($metas[$key][$array_key])) ? $metas[$key] : $return_type;
      } else {
        // return key group
        $result = (isset($metas[$key]) && !empty($metas[$key]) && isset($metas[$key][$array_key])) ? $metas[$key][$array_key] : $return_type;
      }
    }

    return $result;
  }

  public static function getImageSizes($unset_disabled = true)
  {
    $wais = & $GLOBALS['_wp_additional_image_sizes'];

    $sizes = array();

    foreach (get_intermediate_image_sizes() as $_size) {
      if ( in_array($_size, ['thumbnail', 'medium', 'medium_large', 'large']) ) {
        $sizes[ $_size ] = [
          'width'  => get_option( "{$_size}_size_w" ),
          'height' => get_option( "{$_size}_size_h" ),
          'crop'   => (bool) get_option( "{$_size}_crop" ),
        ];
      }
      elseif ( isset( $wais[$_size] ) ) {
        $sizes[ $_size ] = [
          'width'  => $wais[ $_size ]['width'],
          'height' => $wais[ $_size ]['height'],
          'crop'   => $wais[ $_size ]['crop'],
        ];
      }

      // size registered, but has 0 width and height
      if( $unset_disabled && ($sizes[ $_size ]['width'] == 0) && ($sizes[ $_size ]['height'] == 0) )
        unset($sizes[ $_size ]);
    }

    return $sizes;
  }
}

?>
