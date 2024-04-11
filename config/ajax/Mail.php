<?php

namespace tnwpt\ajax;

use tnwpt\helpers\View;
use tnwpt\custom\AdminOptionsPage;

class Mail
{
    public $from_name;
    public $from_email;

    public function register()
    {
        $this->from_name = str_replace(['http://', 'https://'], '', get_home_url());
        // $this->from_name = preg_replace('/(http)+(s)*:\/\/([a-z0-9])+\./i', '', get_home_url()); // subdomain
        $this->from_email = 'msg@' . $this->from_name;

        $this->register_mail_actions();
    }

    private function register_mail_actions()
    {
        add_action('phpmailer_init', [&$this, 'action_phpmailer_init']);
        add_action('wp_mail_failed', [&$this, 'action_wp_mail_failed'], 10, 1);

        add_action('wp_ajax_mail', [&$this, 'action_wpajax_mail']);
        add_action('wp_ajax_nopriv_mail', [&$this, 'action_wpajax_mail']);
    }

    /**
     * PHPMAILER
     * See .env
     */
    public function action_phpmailer_init($phpmailer)
    {
        if ( isset($_ENV['MAIL_SMTP']) && $_ENV['MAIL_SMTP'] ) {
            $phpmailer->IsSMTP();
            $phpmailer->CharSet = 'UTF-8';
            $phpmailer->Host = $_ENV['MAIL_SMTP_HOST'];
            $phpmailer->Username = $_ENV['MAIL_SMPT_USERNAME'];
            $phpmailer->Password = $_ENV['MAIL_SMTP_PASSWORD'];
            $phpmailer->SMTPAuth = $_ENV['MAIL_SMTP_AUTH'];
            $phpmailer->SMTPSecure = $_ENV['MAIL_SMTP_SECURE'];
            $phpmailer->Port = $_ENV['MAIL_SMTP_PORT'];
            $phpmailer->From = $_ENV['MAIL_SMTP_FROM'];
            $phpmailer->FromName = $this->from_name;
        }

        $phpmailer->isHTML( true );
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
        $sbj = "Заявка с {$this->from_name}";

        // Сообщение
        $msg = '';
        ob_start();
        include(locate_template('views/email/template-email.php'));
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
                'data' => $data,
                'email' => $to,
                'sbj' => $sbj,
                'msg' => $msg,
                // 'url' => '/'
            ]);
        }

        if (!$to) {
            wp_send_json_error(['msg' => 'Не установлен адресат в настройках']);
        }

        $headers = [
            'content-type: text/html',
            "From: {$this->from_name} <{$this->from_email}>"
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
}
