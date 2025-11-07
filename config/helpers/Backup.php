<?php
namespace tnwpt\helpers;

class Backup
{
    private $path = '';
    private $filename = 'backup-site.zip';

    public function register()
    {
        $this->path = ABSPATH;

        $this->checkBackupDir();

        // -- Add cron job 'tnwpt_backup_cron'
        add_action('tnwpt_backup_cron', [&$this, 'action_backup_cron']);

        add_action('cmb2_admin_init', [&$this, 'action_cmb2_admin_init']);

        add_action('wp_ajax_create_backup_file', [ &$this, 'createBackupFile']);
        add_action('wp_ajax_delete_backup_file', [ &$this, 'deleteBackupFile']);
    }

    public function checkBackupDir()
    {
        if ( !is_dir($this->path) ) {
            mkdir($this->path);
        }
    }

    public function action_backup_cron()
    {
        $this->createBackup();
    }

    public function createBackup()
    {
        ini_set("max_execution_time", 0);
        ini_set("max_input_time", 0);
        ini_set('memory_limit', '2048M');

        $dir = array_diff(scandir(ABSPATH), ['..', '.']);

        $files = array_filter($dir, function($item) {
            return (is_link($item)) ? false : true;
        });

        if (!$files) return;

        if ( file_exists(ABSPATH . $this->filename) ) {
            unlink(ABSPATH . $this->filename);
        }

        $zip = new \ZipArchive();
        $zip->open(ABSPATH . $this->filename, \ZipArchive::CREATE|\ZipArchive::OVERWRITE);

        $this->addFileRecursion($zip, ABSPATH);

        $zip->close();
    }

    public function addFileRecursion($zip, $dir, $start = '')
    {
        if (empty($start)) {
            $start = $dir;
        }

        $objs = array_merge(glob($dir . '/.[!.]*'), glob($dir . '/*'));

        if ($objs) {
            foreach($objs as $obj) {

            if (is_link($obj)) continue;

            if (is_dir($obj)) {
                if ( stristr($obj, 'node_modules') ) continue;

                $this->addFileRecursion($zip, $obj, $start);
            } else {
                if ( stristr($obj, '.lock') ) continue;
                $zip->addFile($obj, str_replace($start . '/', '', $obj));
            }
            }
        }
    }

    public function action_cmb2_admin_init()
    {
        $list = $this->backupList();

        $nonce = wp_create_nonce($_ENV['MAIL_NONCE']);

        $mb = new_cmb2_box([
            'id' => 'backup_files',
            'title' => 'Резервная копия сайта',
            'object_types' => [ 'options-page' ],
            'parent_slug' => 'tools.php',
            'option_key' => 'tnwpt_backup_files',
        ]);
        $mb->add_field([
            'id' => 'backup_files_title',
            'name' => '',
            'desc' => "
                <p>Папка, где хранятся копии: {$this->path}{$this->filename}</p>
                <p>Расписание устанавливается через плагин <a href='/wp-admin/tools.php?page=crontrol_admin_manage_page'>WP Crontrol</a>. Задание '<strong>tnwpt_backup_cron</strong>'</p>
                <p><button type='button' id='create-backup-files' data-token='{$nonce}'>Создать резервную копию</button></p>
                ",
            'type' => 'title'
        ]);
        $mb->add_field([
            'id' => 'backup_files_title_list',
            'name' => 'Текущие копии',
            'desc' => $list,
            'type' => 'title'
        ]);
    }

    public function backupList()
    {
        $nonce = wp_create_nonce($_ENV['MAIL_NONCE']);

        $html = '<div class="backup-list"><ul>';

        if ( file_exists(ABSPATH . 'backup-site.zip') ) {
            $html .= "<li>{$this->filename} <a href='/{$this->filename}'>Скачать</a> <button type='button' class='js-backup-files-delete' data-file='{$this->filename}' data-token='{$nonce}'><span class='dashicons dashicons-trash'></span></button></li>";
        }

        $html .= '</ul></div>';

        return $html;
    }

    public function createBackupFile()
    {
        // проверяем nonce код, если проверка не пройдена прерываем обработку
        if (!wp_verify_nonce($_POST['token'], $_ENV['MAIL_NONCE'])) {
            wp_send_json_error(['msg' => 'Fail']); // Check failed
        }

        $this->createBackup();

        wp_send_json_success();

        die();
    }

    public function deleteBackupFile()
    {
        // проверяем nonce код, если проверка не пройдена прерываем обработку
        if (!wp_verify_nonce($_POST['token'], $_ENV['MAIL_NONCE'])) {
            wp_send_json_error(['msg' => 'Fail']); // Check failed
        }

        $path = ABSPATH . $this->filename;

        unlink($path);

        wp_send_json_success();

        die();
    }
}
