<?php

namespace tnwpt\helpers;

class ImageOptimizer
{
    public function register()
    {
        add_action('admin_head', [&$this, 'setupCron']);
        add_action('tnwpt_regenerateimg_cron', [&$this, 'actionCron']);

        add_filter('jpeg_quality', [ &$this, 'jpeg_quality' ], 10, 1);
        add_filter('big_image_size_threshold', '__return_false');

        $this->checkOriginalImagesFolder();

        add_filter('wp_generate_attachment_metadata', [ &$this, 'optimizeImageStandart'], 10, 3);
        add_filter('pre_delete_attachment', [ &$this, 'deleteOriginalAttachment'], 10, 3);

        add_action('cmb2_admin_init', [&$this, 'adminControlPage']);

        add_action('wp_ajax_regenerateThumbs', [ &$this, 'regenerateThumbsAjax']);
        add_action('wp_ajax_regenerateThumbsStatus', [ &$this, 'regenerateThumbsStatus']);
    }

    public function setupCron()
    {
        if (!wp_next_scheduled('tnwpt_regenerateimg_cron')) {
            wp_schedule_single_event(time(), 'tnwpt_regenerateimg_cron');
        }
    }

    public function actionCron()
    {
        $this->regenerateThumbs();
    }

    public function adminControlPage()
    {
        $mb = new_cmb2_box([
            'id' => 'regenerate-thumbs',
            'title' => 'Перегенерировать картинки',
            'object_types' => [ 'options-page' ],
            'parent_slug' => 'tools.php',
            'option_key' => 'tnwpt_regenerate_thumbs',
            'save_button' => '-'
        ]);

        $mb->add_field([
            'id' => 'images_count',
            'name' => 'Всего изображений',
            'type' => 'text',
            'save_field' => false,
            'attributes' => ['class' => 'large-text', 'readonly' => true],
            'default_cb' => [&$this, 'imagesCount']
        ]);
        $mb->add_field([
            'id' => 'images_status',
            'name' => 'Статус',
            'type' => 'text',
            'attributes' => ['class' => 'large-text', 'readonly' => true],
            'default' => 0
        ]);
        $mb->add_field([
            'id' => 'images_btn',
            'type' => 'text',
            'save_field' => false,
            'attributes' => ['class' => 'large-text', 'readonly' => true],
            'render_row_cb' => [&$this, 'regenerateThumbsButton']
        ]);
    }

    public function imagesCount($field_args, $field)
    {
        $posts = get_posts([
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'post_mime_type' => 'image',
            'fields' => 'ids',
            'posts_per_page' => -1
        ]);

        return count($posts);
    }

    public function regenerateThumbsButton($field_args, $field)
    {
        $nonce = wp_create_nonce($_ENV['MAIL_NONCE']);

        $html = "<div class='cmb-row cmb-type-text cmb2-id-images-status table-layout'>
        <p>Не уходите с этой страницы до конца операции</p>
        <input type='button' name='submit-cmb' id='regenerate-thumbs-btn' class='button button-primary' value='Пересоздать картинки' data-token='{$nonce}'>
        </div>";

        echo $html;
    }

    public function regenerateThumbs()
    {
        require ABSPATH . 'wp-admin/includes/image.php';

        $get_upload_dir = wp_upload_dir();
        $upload_dir = $get_upload_dir['basedir'];

        $opts = get_option('tnwpt_regenerate_thumbs', false);
        $count = $opts['images_status'];

        $attachment_ids = get_posts([
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'post_mime_type' => 'image',
            'fields' => 'ids',
            'posts_per_page' => -1
        ]);

        $count = 1;
        $total = count($attachment_ids);

        $opts['images_status'] = 0;
        update_option('tnwpt_regenerate_thumbs', $opts);

        // Delete Files
        foreach($attachment_ids as $id) {
            $meta = wp_get_attachment_metadata($id);
            $backup_sizes = get_post_meta($id, '_wp_attachment_backup_sizes', true);
            $file = get_attached_file($id);

            wp_delete_attachment_files($id, $meta, $backup_sizes, $file );
        }

        $files = [];

        // Copy from Original
        foreach($attachment_ids as $id) {
            $opts['images_status'] = $count;
            update_option('tnwpt_regenerate_thumbs', $opts);

            $file = get_attached_file( $id );
            $filename = pathinfo($file, PATHINFO_BASENAME);
            $original_file = "{$upload_dir}/originals/$filename";
            $to_copy = "{$upload_dir}/${filename}";

            $copy = copy($original_file, $to_copy);

            if ( $copy ) {
                $new_meta = wp_generate_attachment_metadata($id, $file);
                $count++;
            }
        }

        $opts['images_status'] = 0;
        update_option('tnwpt_regenerate_thumbs', $opts);
    }

    public function regenerateThumbsAjax()
    {
        // проверяем nonce код, если проверка не пройдена прерываем обработку
        if (!wp_verify_nonce($_POST['token'], $_ENV['MAIL_NONCE'])) {
            wp_send_json_error(['msg' => 'Fail']); // Check failed
        }

        // $this->regenerateThumbs();
        $this->setupCron();
        $this->regenerateThumbsStatus();

        return;
    }

    public function regenerateThumbsStatus()
    {
        $opts = get_option('tnwpt_regenerate_thumbs', false);
        $count = $opts['images_status'];

        wp_send_json_success($count);
    }

    public function jpeg_quality($q)
    {
        return 100;
    }

    public function checkOriginalImagesFolder()
    {
        $upload_dir = wp_get_upload_dir();
        $original_folder = "{$upload_dir['basedir']}/originals";

        if (!is_dir($original_folder)) {
            mkdir($original_folder, 0777);
        }
    }

    public function optimizeImageStandart($image_meta, $attachment_id, $string)
    {
        if (!wp_attachment_is_image($attachment_id)) return;

        $ext = pathinfo($image_meta['file'], PATHINFO_EXTENSION);

        if (!in_array($ext, ['png', 'jpg'])) return $image_meta;

        $this->checkOriginalImagesFolder();

        $upload_dir = wp_get_upload_dir();

        $meta = wp_get_attachment_metadata($attachment_id);
        $original = $meta['file'];
        $sizes = $meta['sizes'];

        // Copy Original File to 'originals'
        $original_path = "{$upload_dir['basedir']}/{$original}";
        $original_path_to = "{$upload_dir['basedir']}/originals/{$original}";
        copy($original_path, $original_path_to);

        foreach ($sizes as $size) {
            $path = "{$upload_dir['basedir']}/{$size['file']}";
            chmod($path, 0777);
            $image = wp_get_image_editor($path);

            if (!is_wp_error($image)) {
                $image->set_quality(80);
                $image->save($path);
            }
        }

        unset($image);

        $image = wp_get_image_editor($original_path);
        $image->resize(2048, 2048, false);
        $image->set_quality(80);
        $image->save($original_path);

        return $image_meta;
    }

    public function deleteOriginalAttachment($delete, $post, $force_delete)
    {
        if ( !wp_attachment_is_image($post->ID) ) return $delete;

        $post = get_post($post->ID);

        $upload_dir = wp_get_upload_dir();

        $meta = wp_get_attachment_metadata($post->ID);
        $original = $meta['file'];

        $original_path = "{$upload_dir['basedir']}/originals/{$original}";

        // Delete Original File
        unlink($original_path);

        return $delete;
    }
}

?>
