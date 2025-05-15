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

        self::$home_url = get_home_url(null,'/');

        self::$templates = get_option('_page_templates');

        return;
    }

    public static function getOpt($key = '')
    {
      if ( function_exists('cmb2_get_option') ) {
        return cmb2_get_option($_ENV['APPOPTSKEY'], $key, false);
      }

      $opts = get_option($_ENV['APPOPTSKEY'], false);

      $val = false;

      if ( $key == 'all') {
        $val = $opts;
      } elseif (is_array( $opts ) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
      }

      return $val;
    }

    public static function getPagesTemplate()
    {
        global $wpdb;

        $query = "
            SELECT post_id, meta_value as template
            FROM $wpdb->postmeta
            WHERE meta_key = '_wp_page_template'
        ";

        $result = $wpdb->get_results($query);

        $data = [];

        foreach ($result as $one) {
            $data[$one->post_id] = $one->template;
        }

        return $data;
    }

    /**
     * Get Phone Number
     */
    public static function getPhone($number, $link = false, $classes = '')
    {
        if ( !$number ) return;

        $num = preg_replace('/[^0-9]/', '', $number);

        if ( substr($num, 0, 1) != '8' ) {
            $num = "+{$num}";
        }

        if ( $link ) {
            $classes = ( $classes ) ? "{$classes} " : '';

            return "<div class='{$classes}js-tel-mobile' data-tel='{$num}'>{$number}</div>";
            // "<a href='tel:{$num}' class='{$classes}'>{$number}</a>";
        }

        return $num;
    }

    /**
     * Get E-mail link
     */
    public static function getEmail($email, $classes = '')
    {
        $mail = sanitize_email($email);

        $classes = ( $classes ) ? "{$classes} " : '';

        return "<div class='{$classes}js-mail' data-mail='{$mail}'></div>";
        // return "<a href='mailto:{$mail}' class='{$classes}'>{$email}</a>";
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

        $home_url = self::getRelativeUrl(self::$home_url);
        $url = self::getRelativeUrl(wp_get_attachment_image_url($logo_id, 'small'));
        $name = get_bloginfo('name');
        $desc = get_bloginfo('description');

        $html = '';

        if ($type == 'img') {
            if ($link) {
                $html = "
                    <a href='{$home_url}' class='{$classes}'>
                        <img src='{$url}' title='{$desc}' alt='{$name}' loading='lazy' decoding='async'/>
                    </a>"
                ;
            } else {
                $html = "<div class='{$classes}'><img src='{$url}' title='{$desc}' alt='{$name}' loading='lazy' decoding='async'/></div>";
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

    public static function getSvg($icon_id, $classes = '', $color = false)
    {
        if (!$icon_id) return;

        $base = self::getRelativeUrl(self::$url);

        $url = "{$base}/assets/icons/sprite.svg#";

        if ( $color ) {
            $url = "{$base}/assets/icons/sprite-color.svg#";
        }

        return "<svg class='ico{$classes}'><use xlink:href='{$url}{$icon_id}'></use></svg>";
    }

    /**
     * Get Plural Form
     * getPluralForm($number, ['вариант', 'варианта', 'вариантов']);
     */
    public static function getPluralForm($number = 0, $after = [])
    {
        $cases = [2, 0, 1, 1, 1, 2];

        $tail = $after[($number % 100 > 4 && $number % 100 < 20) ? 2: $cases[min($number % 10, 5)]];

        return "{$number} {$tail}";
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
        global $wpdb;

        if ( !$post_id || !$keys ) return;

        $bit = $_ENV['CMB'];

        $meta_bit = array_map(function($item) use ($bit) {
            return "'{$bit}_{$item}'";
        }, $keys);

        $meta_lodash = array_map(function($item) {
            return "'_{$item}'";
        }, $keys);

        $metas = array_merge($meta_bit, $meta_lodash);
        $metas = implode(',', $metas);

        $query = "
            SELECT meta_key, meta_value
            FROM $wpdb->postmeta
            WHERE post_id = {$post_id}
                AND meta_key IN ({$metas})
        ";

        $result = $wpdb->get_results($query);

        $data = [];

        foreach($result as $one) {
            $key = preg_replace(["/(^_)+/", "/(^_{$bit}_)+/"], '', $one->meta_key);
            $data[$key] = maybe_unserialize($one->meta_value);
        }

        return $data;
    }

    public static function getTermMeta($term_id = 0, $keys = [])
    {
        if (!$term_id || !$keys) return [];

        $bit = $_ENV['CMB'];

        $result = [];

        foreach($keys as $key) {
            $meta = get_term_meta($term_id, "{$bit}_{$key}", 1);

            if ( $meta ) {
                $result[$key] = $meta;
            } else {
                $result[$key] = false;
            }
        }

        return $result;
    }

    public static function getTermsData($tax = '', $ids = [], $keys = [])
    {
        if ( !$ids ) return;

        $prefix = $_ENV['CMB'];

        $arr = [];

        global $wpdb;

        $keys_sql = ($keys) ? implode(',', array_map(function($var) use ($prefix) {
            return "'{$prefix}_{$var}'";
        }, $keys)) : '';

        $ids_sql = implode(',', $ids);

        $sql = "
            SELECT tt.term_id, t.name, t.slug
            FROM $wpdb->term_taxonomy as tt
            INNER JOIN $wpdb->terms as t ON tt.term_id = t.term_id
            WHERE tt.taxonomy = '{$tax}'
            AND tt.term_id IN ({$ids_sql})
        ";

        if ($keys_sql) {
            $sql = "
                SELECT tt.term_id, t.name, t.slug, tm.meta_key, tm.meta_value
                FROM $wpdb->term_taxonomy as tt
                INNER JOIN $wpdb->terms as t ON tt.term_id = t.term_id
                INNER JOIN $wpdb->termmeta as tm ON tt.term_id = tm.term_id
                WHERE tt.taxonomy = '{$tax}'
                AND tt.term_id IN ({$ids_sql})
                AND tm.meta_key IN ({$keys_sql})
            ";
        }

        $res = $wpdb->get_results($sql, ARRAY_A);

        if (!$res) return $arr;

        foreach($res as $one) {
            $arr[$one['term_id']]['link'] = self::getRelativeUrl(get_term_link((int)$one['term_id'], $tax));
            $arr[$one['term_id']]['term_id'] = $one['term_id'];
            $arr[$one['term_id']]['name'] = $one['name'];
            $arr[$one['term_id']]['slug'] = $one['slug'];

            if ( self::checkArray($one, 'meta_key') ) {
                $arr[$one['term_id']][ str_replace("{$prefix}_", '', $one['meta_key']) ] = maybe_unserialize($one['meta_value']);
            }
        }

        $result = [];

        foreach($ids as $termid) {
            if ( array_key_exists($termid, $arr) ) {
                $result[$termid] = $arr[$termid];
            }
        }

        unset($arr);

        return $result;
    }

    /**
     * Get metas for many objects
     * @return array
     */
    public static function getPostsData($post_ids = [], $fields = [], $metas = [])
    {
        global $wpdb;

        $post_ids = array_filter($post_ids, function($item) {
            return ( !empty($item) ) ? true : false;
        });

        if ( !$post_ids ) return [];

        $fields = array_filter($fields, function($item) {
            return ( !empty($item) ) ? true : false;
        });

        $metas = array_filter($metas, function($item) {
            return ( !empty($item) ) ? true : false;
        });

        $bit = $_ENV['CMB'];

        $data = [];

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

                $data[$pid]['link'] = self::getRelativeUrl(get_permalink($pid));
            }
        }

        $meta_bit = array_map(function($item) use ($bit) {
            return "'{$bit}_{$item}'";
        }, $metas);

        $meta_lodash = array_map(function($item) {
            return "'_{$item}'";
        }, $metas);

        $metas = array_merge($meta_bit, $meta_lodash);

        $metas[] = "'_thumbnail_id'";

        $metas = implode(',', $metas);

        $sql = "
            SELECT post_id, meta_key, meta_value
            FROM $wpdb->postmeta
            WHERE post_id IN ($ids_str) AND meta_key IN ($metas)
        ";

        $query = $wpdb->get_results($sql, 'ARRAY_A');

        if ($query) {
            foreach($query as $v) {
                $key = preg_replace(["/(^_)+/", "/(^_{$bit}_)+/"], '', $v['meta_key']);
                $data[$v['post_id']][$key] = maybe_unserialize($v['meta_value']);
            }
        }

        return $data;
    }

    /**
     * Check key, value in array
     * @return boolean
     */
    public static function checkArray($array = [], $key = '', $value = '')
    {
        if ( !$array ) return false;
        if ( !$key ) return false;

        if ( $value ) {
            if ( isset($array[$key]) && !empty($array[$key]) ) {
                if ( $value == $array[$key] ) {
                    return true;
                }

                return true;
            }

            return false;
        }

        if ( array_key_exists($key, $array) && !empty($array[$key]) ) {
            return true;
        }

        return false;
    }

    /**
     * Check & return meta value from meta array
     * @return value or default value
     */
    public static function checkMeta($metas = [], $key = '', $return_default = '', $return_key = -1)
    {
        $result = $return_default;

        if ( !$metas ) return $result;
        if ( !$key ) return $result;

        if ($return_key >= 0) {
            $result = ( self::checkArray($metas, $key) ) ? $metas[$key][$return_key] : $return_default;

            return $result;
        }

        $result = ( self::checkArray($metas, $key) ) ? $metas[$key] : $return_default;

        return $result;
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
     * Check $_POST data
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
            'order' => 'ASC',
        ]);

        $items_data = self::getPostsData($items, ['post_title'], []);

        if ($ids) {
            foreach($ids as $post_id) {
                if ( array_key_exists($post_id, $items_data) ) {
                    $data[$post_id] = $items_data[$post_id]['title'];
                    unset($items_data[$post_id]);
                }
            }
        }

        if ($items_data && $add) {
            foreach($items_data as $one) {
                $data[$one['ID']] = $one['title'];
            }
        }

        return $data;
    }

    public static function getTaxOrder($ids = [], $tax = [], $add = true, $posts = [])
    {
        $data = [];

        $args = [
            'taxonomy' => $tax,
            'orderby' => 'id',
            'hide_empty' => false,
            'order' => 'ASC',
            'fields' => 'id=>name'
        ];

        if ($posts) {
            $args['object_ids'] = $posts;
        }

        $items = get_terms($args);

        if ($ids) {
            foreach($ids as $term_id) {
                if ( array_key_exists($term_id, $items) ) {
                    $data[$term_id] = $items[$term_id];
                    unset($items[$term_id]);
                }
            }
        }

        if ($items && $add) {
            foreach($items as $term_id => $term_name) {
                $data[$term_id] = $term_name;
            }
        }

        return $data;
    }

    public static function sendCurl($url = '', $type = 'get', $postdata = [], $return_type = 'json', $headers = [])
      {
        if (!$url) return;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // для отладки раскомментируйте и просмотрите файл
        // $fp = fopen(dirname(__FILE__).'/_curl_error_log.txt', 'w');
        // curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // curl_setopt($ch, CURLOPT_STDERR, $fp);

        if ($type == 'get') {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

            $curl_headers = [];
            $curl_headers = array_merge($curl_headers, $headers);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
        }

        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            $curl_headers = [
                'Content-Type: application/json'
            ];
            $curl_headers = array_merge($curl_headers, $headers);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE));
        }

        $result = curl_exec($ch);

        curl_close($ch);

        if ($return_type && $return_type == 'json') {
            return json_decode($result, true);
        }

        return $result;
    }

    /**
     * Breadcrumbs
     */
    public static function breadcrumbs($classes = '')
    {
        $classes = ( $classes ) ? " {$classes}" : '';
        $sep = '<li class="breadcrumbs-separator">&nbsp;&mdash;&nbsp;</li>';
        $position = 1;

        $get_item_html = function($title = '', $link = '', $position = 1, $current = false)
        {
            $html = '';

            $schema_li = 'itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"';

            if ( !$current ) {
                $html = "
                <li {$schema_li} class='breadcrumbs-item'>
                    <a class='breadcrumbs-link' href='{$link}' itemprop='item'>
                    <span itemprop='name'>{$title}</span>
                    </a>
                    <meta itemprop='position' content='{$position}'>
                </li>
                ";

                return $html;
            }

            $html .= "
                <li {$schema_li} class='breadcrumbs-item is-current'>
                <span itemprop='name'>{$title}</span>
                <meta itemprop='position' content='{$position}'>
                </li>
            ";

            return $html;
        };

        $html = "<ul class='breadcrumbs{$classes}' itemscope itemtype='https://schema.org/BreadcrumbList'>";

        // Main page
        $html .= $get_item_html('Главная', get_home_url(), $position);

        $position = 2;

        // Page
        if ( is_page() ) {
            global $post;

            $html .= $sep;
            $html .= $get_item_html($post->post_title, '', $position, true);
        }

        $html .= '</ul>';

        return $html;
    }

    public static function explodeStrings($sep1 = '', $sep2 = '', $string = '')
    {
        if ( !$string || !$sep1 ) return $string;

        $result = $string;

        $result = ($result) ? explode($sep1, $result) : [];

        $result = ($result) ? array_map(function($item) use ($sep2) {
            $str = explode($sep2, $item);
            return [
                'name' => trim($str[0]),
                'value' => trim($str[1]),
            ];
        }, $result) : [];

        return $result;
    }

    public static function getFormFields()
    {
        $html = '
            <input type="text" name="mouse" value="'. wp_generate_password(12,true) .'" class="v-d-none">
            <input type="hidden" name="title" value="'. wp_get_document_title() .'">
            <input type="hidden" name="url" value="'. get_self_link() .'">
            <input type="hidden" name="sbj" value="'. wp_get_document_title() .'">

            <input type="hidden" name="utm[UTM_CAMPAIGN]" value="'. self::checkMeta($_GET, 'utm_campaign', '') .'">
            <input type="hidden" name="utm[UTM_CONTENT]" value="'. self::checkMeta($_GET, 'utm_content', '') .'">
            <input type="hidden" name="utm[UTM_MEDIUM]" value="'. self::checkMeta($_GET, 'utm_medium', '') .'">
            <input type="hidden" name="utm[UTM_SOURCE]" value="'. self::checkMeta($_GET, 'utm_source', '') .'">
            <input type="hidden" name="utm[UTM_TERM]" value="'. self::checkMeta($_GET, 'utm_term', '') .'">
        ';

        return $html;
    }

    public static function getAjaxSanitizedData($data = [])
    {
        $result = [];

        if ( !$data ) return $result;

        $result = [
            'name' => ( self::checkArray($data,'name') ) ? sanitize_text_field($data['name']) : '',
            'tel' => ( self::checkArray($data,'tel') ) ? sanitize_text_field($data['tel']) : '',
            'email' => ( self::checkArray($data,'email') ) ? sanitize_email($data['email']) : '',
            'msg' => ( self::checkArray($data,'msg') ) ? sanitize_textarea_field($data['msg']) : '',
            'subject' => ( self::checkArray($data,'sbj') ) ? sanitize_textarea_field($data['sbj']) : '',
            'title' => ( self::checkArray($data,'title') ) ? sanitize_text_field($data['title']) : '',
            'url' => ( self::checkArray($data,'url') ) ? esc_url($data['url'], ['https', 'http']) : '',
            'utm' => [],
        ];

        if ( self::checkArray($data,'utm') ) {
            foreach($data['utm'] as $key => $one) {
                if (!$one) continue;

                $result['utm'][$key] = $one;
            }
        }

        return $result;
    }
}
