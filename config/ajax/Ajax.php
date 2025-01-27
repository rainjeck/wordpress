<?php

namespace tnwpt\ajax;

use tnwpt\helpers\View;
use tnwpt\custom\AdminOptionsPage;

class Ajax
{
    public $from_name;
    public $from_email;

    public function register()
    {
        $this->actions = [
            'mail',
        ];

        $this->register_mail_actions();
    }

    private function register_mail_actions()
    {
        add_action('phpmailer_init', [&$this, 'action_phpmailer_init']);
        add_action('wp_mail_failed', [&$this, 'action_wp_mail_failed'], 10, 1);

        if ( !$this->actions ) return;

        foreach($this->actions as $one) {
            add_action("wp_ajax_{$one}", [&$this, "action_wpajax_{$one}"]);
            add_action("wp_ajax_nopriv_{$one}", [&$this, "action_wpajax_{$one}"]);
        }
    }

    /**
     * PHPMAILER
     * See .env
     */
    public function action_phpmailer_init($phpmailer)
    {
        $phpmailer->isHTML(true);
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->From = $_ENV['MAIL_FROM'];
        $phpmailer->FromName = $_ENV['DOMAIN'];

        // $phpmailer->DKIM_domain = $_ENV['DOMAIN'];
        // $phpmailer->DKIM_private = ABSPATH . '/dkim_private.pem';
        // $phpmailer->DKIM_selector = 'mail';
        // $phpmailer->DKIM_identity = $_ENV['MAIL_FROM'];

        if ( isset($_ENV['MAIL_SMTP']) && $_ENV['MAIL_SMTP'] ) {
            $phpmailer->IsSMTP();
            $phpmailer->Host = $_ENV['MAIL_SMTP_HOST'];
            $phpmailer->Username = $_ENV['MAIL_SMPT_USERNAME'];
            $phpmailer->Password = $_ENV['MAIL_SMTP_PASSWORD'];
            $phpmailer->SMTPAuth = $_ENV['MAIL_SMTP_AUTH'];
            $phpmailer->SMTPSecure = $_ENV['MAIL_SMTP_SECURE'];
            $phpmailer->Port = $_ENV['MAIL_SMTP_PORT'];
            $phpmailer->From = $_ENV['MAIL_SMTP_FROM'];
        }
    }

    /**
     * Выведем ошибку в .log файл
     */
    public function action_wp_mail_failed($wp_error)
    {
        error_log( $wp_error->get_error_message() );
    }

    public function action_wpajax_mail()
    {
        $data = View::checkAjaxData(); // если FormData

        // Тема письма
        $sbj = "Заявка с {$_ENV['DOMAIN']}";

        $postdata = $this->getDataSanitizeArray($data);

        // Сообщение
        $msg = '';
        ob_start();
            get_template_part('views/email/template', 'email', ['data' => $postdata]);
            $msg = ob_get_contents();
        ob_end_clean();

        // Files
        // $files = [];

        // if ($_FILES) {
        //   $files_errors = [];

        //   $files_arr = $this->postFilesData( $_FILES['files'] );

        //   foreach ($files_arr as $file) {
        //     $file_upload = wp_handle_upload( $file, ['test_form' => false], date('Y/m') );

        //     if ( $file_upload && empty($file_upload['error']) ) {
        //       $files[] = $file_upload;
        //     } else {
        //       $files_errors[] = 'Файл '. $file['name'] .' не загружен';
        //     }
        //   }

        //   if ( $files_errors ) {
        //     wp_send_json_error([
        //       'file_errors' => $files_errors
        //     ]);
        //   }
        // }

        // отправляем на email, указанный в опциях сайта
        $to = View::getOpt('email_order');

        // URL, на который отправим после отправки письма
        // $page_thanks_id = View::getPostIdByTemplate('template-page-thanks.php');
        // $url = get_the_permalink($page_thanks_id);

        // Отладка
        if ($_ENV['MAIL_DEV']) {
            wp_send_json_success([
                '$data' => $data,
                '$email' => $to,
                '$sbj' => $sbj,
                '$msg' => $msg,
                // 'url' => '/'
            ]);
        }

        if (!$to) {
            wp_send_json_error(['msg' => 'Не установлен адресат в настройках']);
        }

        $headers = [
            'content-type: text/html',
            "From: {$_ENV['DOMAIN']} <{$_ENV['MAIL_FROM']}>"
        ];

        // Отправка
        $mail = wp_mail($to, $sbj, $msg, $headers);

        if ( $mail ) {
            // foreach ($files as $file) {
            //   unlink($file['file']);
            // }

            wp_send_json_success([
                // 'url' => $url
            ]);
        } else {
            wp_send_json_error('Somethings went wrong. See logs');
        }
    }

    private function postFilesData($files)
    {
        if (!$files) return [];

        $filesData = [];

        $types = ['msword', 'vnd.openxmlformats-officedocument.wordprocessingml.document', 'plain', 'rtf', 'pdf', 'jpeg', 'png', 'tiff'];
        $size = 2; // limit 2 mb

        // Фильтры
        $file_size = $size * pow(1024, 2); // 2 mb

        // Для загрузки
        $filepath = wp_upload_dir();

        $filesArr = [];

        foreach($files['name'] as $key => $value) {
            $filesArr[] = [
                'name' => $value,
                'type' => explode('/', $files['type'][$key])[1],
                'size' => $files['size'][$key],
                'tmp_name' => $files['tmp_name'][$key]
            ];
        }

        return $filesArr;
    }

    private function getDataSanitizeArray($data)
    {
        $result = [];

        if (!$data) return $data;

        $result = [
            'name' => (isset($data['name']) && !empty($data['name'])) ? sanitize_text_field($data['name']) : '',
            'tel' => (isset($data['tel']) && !empty($data['tel'])) ? sanitize_text_field($data['tel']) : '',
            'email' => (isset($data['email']) && !empty($data['email'])) ? sanitize_email($data['email']) : '',
            'msg' => (isset($data['msg']) && !empty($data['msg'])) ? sanitize_textarea_field($data['msg']) : '',
            'subject' => (isset($data['sbj']) && !empty($data['sbj'])) ? sanitize_textarea_field($data['sbj']) : '',
            'title' => (isset($data['title']) && !empty($data['title'])) ? sanitize_text_field($data['title']) : '',
            'url' => (isset($data['url']) && !empty($data['url'])) ? esc_url($data['url'], ['https', 'http']) : '',
            'utm' => [],
        ];

        if ( isset($data['utm']) ) {
            foreach($data['utm'] as $key => $one) {
                if (!$one) continue;

                $result['utm'][$key] = $one;
            }
        }

        return $result;
    }
}
