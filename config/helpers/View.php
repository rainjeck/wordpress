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

    /**
     * Phone Number
     */
    public static function getPhone($number, $link = false, $classes = '')
    {
        if ( !$number ) return;

        $num = preg_replace('/[^0-9]/', '', $number);

        if ($link) {
            return "<a href='tel:+{$num}' class='{$classes}'>{$number}</a>";
        }

        return $num;
    }

    /**
     * E-mail link
     */
    public static function getEmail($email, $classes = '')
    {
        $mail = sanitize_email($email);

        return "<a href='mailto:{$mail}' class='{$classes}'>{$email}</a>";
    }

    /**
     * Get relative URL
     */
    public static function getRelativeUrl($url)
    {
        $result = preg_replace('/(http|https):\/\/(?:.*?)\//i', '/', $url);

        return $result;
    }

    /**
     * Custom Logo
     * type - img, inline, bg
     */
    public static function getLogo($type = 'img', $classes = '', $link = true)
    {
        $logo_id = get_theme_mod('custom_logo');

        if (!$logo_id) return '';

        $classes = ($classes) ? $classes : '';

        $home_url = self::$home_url;
        $url = wp_get_attachment_image_url($logo_id, 'small');
        $name = get_bloginfo('name');
        $desc = get_bloginfo('description');

        $html = '';

        if ($type == 'img') {
            if ($link) {
                $html = "
                    <a href='{$home_url}' class='{$classes}'>
                        <img src='{$url}' title='{$desc}' alt='{$name}'/>
                    </a>"
                ;
            } else {
                $html = "<img src='{$url}' title='{$desc}' alt='{$name}' class='{$classes}'/>";
            }
        }

        if ($type == 'bg') {
            $style = "style='background-image: url({$url})'";

            if ($link) {
                $html = "<a href='{$home_url}' class='{$classes}'{$style}></a>";
            } else {
                $html = "<div class='{$classes}'{$style}></div>";
            }
        }

        if ($type == 'inline') {
            $url = ABSPATH . ltrim(self::getRelativeUrl($url),'/');
            $content = file_get_contents($url);

            if (!$content) return '';

            if ($link) {
                $html = "<a href='{$home_url}' class='{$classes}'>{$content}</a>";
            } else {
                $html = "<div class='{$classes}'>{$content}</div>";
            }
        }

        return $html;
    }

    public static function getImg($filename = '', $folder = '')
    {
        $url = self::getRelativeUrl(self::$url);

        return "{$url}/assets/{$folder}/{$filename}";
    }

    public static function getSvg($icon_id, $classes = '')
    {
        if (!$icon_id) return;

        $url = self::$url . '/assets/icons/sprite.svg#';

        return "<svg class='ico{$classes}'><use xlink:href='{$url}{$icon_id}'></use></svg>";
    }

    public static function getSvgColor($icon_id, $classes = '')
    {
        if (!$icon_id) return;

        $url = self::$url . '/assets/icons/sprite-color.svg#';

        return "<svg class='ico{$classes}'><use xlink:href='{$url}{$icon_id}'></use></svg>";
    }

    /**
     * Get Plural Form
     * getPluralForm($number, ['вариант', 'варианта', 'вариантов']);
     */
    public static function getPluralForm($number = 0, $after = [])
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $after[($number % 100 > 4 && $number % 100 < 20) ? 2: $cases[min($number % 10, 5)]];
    }

    public static function wpautop($text)
    {
        if ( !$text ) return;

        $content = wpautop($text);
        $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
        $content = self::getRelativeUrl($content);

        return $content;
    }

    public static function getPostIdByTemplate($template_file)
    {
        if (!$template_file) return;

        $key = array_search($template_file, self::$templates);

        return ($key) ? $key : false;
    }

    public static function getPostMeta($post_id = 0, $keys = [])
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

    public static function checkMeta($metas, $key, $return_default = '', $return_key = -1)
    {
        $result = $return_default;

        if ($return_key >= 0) {
            $result = (isset($metas[$key]) && !empty($metas[$key]) && isset($metas[$key][$return_key])) ? $metas[$key][$return_key] : $return_default;

            return $result;
        }

        $result = (isset($metas[$key]) && !empty($metas[$key])) ? $metas[$key] : $return_default;

        return $result;
    }

    /**
    * Check key & value in array
    * @return boolean
    */
    public static function checkArray($array = [], $key = '', $value = '')
    {
        if (!$array) return false;

        if ($value && isset($array[$key])) {
            return ($value == $array[$key]) ? true : false;
        }

        if ($key && array_key_exists($key,$array)) {
            return true;
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

    /**
     * Check Post Data
     * @return array
     */
    public static function checkAjaxData()
    {
        // проверяем nonce код, если проверка не пройдена прерываем обработку
        if (!wp_verify_nonce($_POST['token'], $_ENV['MAIL_NONCE'])) {
            wp_send_json_error(['msg' => 'Fail']); // Check failed
        }

        // разбираем строку data из ajax
        $data = $_POST; // если FormData

        // проверяем на робота
        if (!empty($data['mouse']) || !isset($data['mouse'])) {
            wp_send_json_error(['msg' => 'You are robot']); // Robot
        }

        return $data;
    }

    public static function writeLog($data)
    {
        if (!WP_DEBUG) return;

        if ( is_array( $data ) || is_object( $data ) ) {
            error_log( print_r( $data, true ) );
        } else {
            error_log( $data );
        }
    }

    public static function getPostsOrder($ids = [], $post_type = '', $add = true)
    {
        $data = [];

        $items = get_posts([
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'orderby' => 'id',
            'order' => 'ASC'
        ]);

        $items_data = self::getPostData($items, ['post_title'], []);

        if ($ids) {
            foreach($ids as $post_id) {
                if ( array_key_exists($post_id, $items_data) ) {
                $data[$post_id] = __($items_data[$post_id]['title']);
                unset($items_data[$post_id]);
                }
            }
        }

        if ($items_data && $add) {
            foreach($items_data as $one) {
                $data[$one['ID']] = __($one['title']);
            }
        }

        return $data;
    }

    /**
     * Breadcrumbs
     */
    public static function breadcrumbs($classes = '')
    {
        $sep = '<li class="breadcrumbs-separator">&nbsp;&mdash;&nbsp;</li>';
        $schema_li = 'itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"';

        $html = "<ul class='breadcrumbs ui-d-flex ui-ul-clear {$classes}' itemscope itemtype='https://schema.org/BreadcrumbList'>";

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
