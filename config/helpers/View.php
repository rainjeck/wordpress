<?php

namespace tnwpt\helpers;

class View
{
    public static $url = '';
    public static $home_url = '';
    public static $templates = [];

    public function register()
    {
        self::$url = get_stylesheet_directory_uri();
        self::$home_url = get_home_url();

        self::$templates = get_option('_page_templates');

        return;
    }

    public static function getOpt($key = '', $default = false)
        {
            if (function_exists('cmb2_get_option')) {
                return cmb2_get_option($_ENV['APPOPTSKEY'], $key, $default);
            }

            $opts = get_option($_ENV['APPOPTSKEY'], $default);

            $val = $default;

            if ('all' == $key) {
                $val = $opts;
            } elseif (is_array( $opts ) && array_key_exists($key, $opts) && false !== $opts[$key]) {
                $val = $opts[$key];
            }

            return $val;
        }

    public static function getPagesTemplate()
    {
        global $wpdb;

        $sql = "
            SELECT post_id, meta_value as template
            FROM $wpdb->postmeta
            WHERE meta_key = '_wp_page_template'
        ";

        $result = $wpdb->get_results($sql);

        $data = [];

        foreach ($result as $one) {
            $data[$one->post_id] = $one->template;
        }

        return $data;
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

    public static function getLogo($classes = '', $link = true, $bg = false)
    {
        $logo_id = get_theme_mod('custom_logo');

        $classes = ($classes) ? $classes : 'customLogo';

        if ($logo_id) {
            $home_url = self::$home_url;
            $url = wp_get_attachment_image_url($logo_id, 'small');
            $name = get_bloginfo('name');
            $desc = get_bloginfo('description');

            $style = '';

            if ($bg) {
                $style = " style='background-image: url({$url})'";
            }

            $html = "<div class='{$classes}'{$style}>";

            if ($link) {
                $html .= "
                <a href='{$home_url}' class='ui-d-block'>
                    <img src='{$url}' title='{$desc}' alt='{$name}'/>
                </a>"
                ;
            }

            if (!$link && !$bg) {
                $html .= "<img src='{$url}' title='{$desc}' alt='{$name}'/>";
            }

            $html .= "</div>";


            return $html;
        }

        return '';
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
        getPluralForm($number, [__('вариант', 'tnwpt'), __('варианта', 'tnwpt'), __('вариантов', 'tnwpt')]);
        */

        $cases = [2, 0, 1, 1, 1, 2];

        return $after[($number % 100 > 4 && $number % 100 < 20) ? 2: $cases[min($number % 10, 5)]];
    }

    public static function wpautop($text)
    {
        if ( !$text ) return;

        $content = wpautop($text);
        $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);

        return $content;
    }

    public static function getPostIdByTemplate($template_file)
    {
        if (!$template_file) return;

        $key = array_search($template_file, self::$templates);

        if ($key) {
            return $key;
        }

        return false;
    }

    public static function getPostMeta($post_id, $keys = [])
    {
        if ( !$post_id || !$keys ) return;

        $prefix = $_ENV['CMB'];

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

    /**
    * Get metas for many objects
    * @return array
    */
    public static function getPostData($post_ids = [], $fields = [], $metas = [], $prefix = true)
    {
        $home_url = self::$home_url;

        $post_ids = array_filter($post_ids, function($item) {
            if (!empty($item)) return true;
            return false;
        });

        if (!$post_ids) return [];

        $fields = array_filter($fields, function($item) {
            if (!empty($item)) return true;
            return false;
        });

        $metas = array_filter($metas, function($item) {
            if (!empty($item)) return true;
            return false;
        });

        $bit = $_ENV['CMB'];

        $data = [];

        global $wpdb;

        $ids_str = implode(',', $post_ids);

        $fields_str = ($fields) ? ', '. implode(', ', $fields) : '';

        $sql = "
            SELECT ID, post_type, post_name $fields_str
            FROM $wpdb->posts
            WHERE ID IN ($ids_str)
        ";

        $query = $wpdb->get_results($sql, 'OBJECT_K');

        if ($query) {
            foreach($post_ids as $pid) {
                foreach($query[$pid] as $k2 => $v2) {
                    $key = str_replace('post_', '', $k2);
                    $data[$pid][$key] = $v2;
                }

                if ( in_array($query[$pid]->post_type, ['post']) ) {
                    $data[$pid]['link'] = "{$home_url}/{$query[$pid]->post_name}";
                } else {
                    $data[$pid]['link'] = "{$home_url}/{$query[$pid]->post_type}/{$query[$pid]->post_name}";
                }
            }
        }

        if ($prefix) {
            $metas = array_map(function($var) use ($bit) {
                return "'{$bit}_{$var}'";
            }, $metas);
        } else {
            $metas = array_map(function($var) use ($bit) {
                return "'{$var}'";
            }, $metas);
        }

        $metas[] = "'_thumbnail_id'";

        $metas_str = implode(',', $metas);

        $sql = "
            SELECT post_id, meta_key, meta_value
            FROM $wpdb->postmeta
            WHERE post_id IN ($ids_str) AND meta_key IN ($metas_str)
        ";

        $query = $wpdb->get_results($sql, 'ARRAY_A');

        if ($query) {
            foreach($query as $v) {
                $key = str_replace(["{$bit}_"], '', $v['meta_key']);
                $data[$v['post_id']][$key] = maybe_unserialize($v['meta_value']);
            }
        }

        return $data;
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

    /**
    * Check key & value in array
    * @return boolean
    */
    public static function checkArray($array = [], $key = '', $value = '')
    {
        if (!$array) return false;

        if ($key) {
            if ($value) {
                return ( isset($array[$key]) && $array[$key] == $value ) ? true : false;
            }

            return (isset($array[$key])) ? true : false;
        }

        return false;
    }

    public static function getImageSizes($unset_disabled = true)
    {
        $wais = & $GLOBALS['_wp_additional_image_sizes'];

        $sizes = array();

        foreach (get_intermediate_image_sizes() as $size) {
            if ( in_array($size, ['thumbnail', 'medium', 'medium_large', 'large']) ) {
                $sizes[$size] = [
                    'width' => get_option( "{$size}_size_w" ),
                    'height' => get_option( "{$size}_size_h" ),
                    'crop' => (bool) get_option( "{$size}_crop" ),
                ];
            }
            elseif ( isset( $wais[$size] ) ) {
                $sizes[ $size ] = [
                    'width' => $wais[ $size ]['width'],
                    'height' => $wais[ $size ]['height'],
                    'crop' => $wais[ $size ]['crop'],
                ];
            }

            // size registered, but has 0 width and height
            if ( $unset_disabled && ($sizes[ $size ]['width'] == 0) && ($sizes[ $size ]['height'] == 0) ) {
                unset($sizes[ $size ]);
            }
        }

        return $sizes;
    }

    public static function breadcrumbs($class = '')
    {
        $sep = '<li class="breadcrumbs-separator">&nbsp;&mdash;&nbsp;</li>';
        $schema_li = 'itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"';

        $html = "<ul class='breadcrumbs ui-d-flex ui-ul-clear {$class}' itemscope itemtype='https://schema.org/BreadcrumbList'>";

        // Main page
        $main_name = 'Главная';
        $main_link = self::$home_url;

        $html .= "<li {$schema_li} class='breadcrumbs-item'><a class='breadcrumbs-link' href='{$main_link}' itemprop='item'><span itemprop='name'>{$main_name}</span></a><meta itemprop='position' content='1'></li>";

        $position = 2;

        // Page
        if ( is_page() ) {
            global $post;

            $ptitle = $post->post_title;

            $html .= $sep;

            $html .= "<li {$schema_li} class='breadcrumbs-item is-current'><span itemprop='name'>{$ptitle}</span><meta itemprop='position' content='{$position}'></li>";
        }

        // Single Post
        if ( is_singular('post') ) {
            global $post;

            $cats = get_the_category($post->ID);

            if ($cats) {
                $cat = array_shift($cats);

                $ptitle = $cat->name;
                $plink = get_category_link($cat->term_id);

                $html .= $sep;

                $html .= "<li {$schema_li} class='breadcrumbs-item'><a class='breadcrumbs-link' href='{$plink}' itemprop='item'><span itemprop='name'>{$ptitle}</span></a><meta itemprop='position' content='{$position}'></li>";

                $position += 1;
            }

            $ptitle = $post->post_title;

            $html .= $sep;

            $html .= "<li {$schema_li} class='breadcrumbs-item is-current'><span itemprop='name'>{$ptitle}</span><meta itemprop='position' content='{$position}'></li>";
        }

        $html .= '</ul>';

        return $html;
    }
}

?>
