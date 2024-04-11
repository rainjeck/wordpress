<?php

namespace tnwpt\helpers;

use Ifsnop\Mysqldump as IMysqldump;

class BackupDB
{
    private $db_path = '';
    private $db_path_url = '';
    private $limit = 0;
    private $date = '';

    public function register()
    {
        $upload_dir = wp_get_upload_dir();
        $this->db_path = "{$upload_dir['basedir']}/backupdb/";
        $this->db_path_url = "{$upload_dir['baseurl']}/backupdb/";

        $this->checkBackupDir();

        // -- Add cron job '_tnwpt_backupdb_cron'
        add_action('_tnwpt_backupdb_cron', [&$this, 'action_backupdb_cron']);

        add_action('cmb2_admin_init', [&$this, 'action_cmb2_admin_init']);

        add_action('wp_ajax_createBackupFile', [ &$this, 'createBackupFile']);
        add_action('wp_ajax_deleteBackupFile', [ &$this, 'deleteBackupFile']);
    }

    public function checkBackupDir()
    {
        if (!is_dir($this->db_path)) {
            mkdir($this->db_path);
        }
    }

    public function action_backupdb_cron()
    {
        $this->createBackup();
    }

    public function action_cmb2_admin_init()
    {
        $list = $this->backupList();

        $nonce = wp_create_nonce($_ENV['MAIL_NONCE']);

        $mb = new_cmb2_box([
            'id' => 'backup',
            'title' => 'Резервные копии БД',
            'object_types' => [ 'options-page' ],
            'parent_slug' => 'tools.php',
            'option_key' => 'tnwpt_backupdb',
        ]);
        $mb->add_field([
            'id' => 'backupdb_title',
            'name' => '',
            'desc' => "
                <p>Папка, где хранятся копии: {$this->db_path}</p>
                <p>Расписание устанавливается через плагин <a href='/wp-admin/tools.php?page=crontrol_admin_manage_page'>WP Crontrol</a>. Задание '<strong>_tnwpt_backupdb_cron</strong>'</p>
                <p><button type='button' id='create-backup' data-token='{$nonce}'>Создать резервную копию</button></p>
                ",
            'type' => 'title'
        ]);
        $mb->add_field([
            'id' => 'limit',
            'name' => 'Сколько хранить копий',
            'desc' => '0 - без ограничений',
            'type' => 'text',
            'attributes' => [ 'class' => 'large-text', 'type' => 'number' ],
            'default' => 0
        ]);
        $mb->add_field([
            'id' => 'backupdb_title_list',
            'name' => 'Текущие копии',
            'desc' => $list,
            'type' => 'title'
        ]);
    }

    public function checkFilesLimit($dbname)
    {
        if (!$this->limit) return;

        $files_cur = [];

        $files = array_values(array_diff(scandir($this->db_path), ['..', '.'])); // files in dir

        $files_cur = array_values(array_filter($files, function($item) use ($dbname) {
            if ( strstr($item, $dbname) ) return true;
            return false;
        }));

        $total = count($files_cur);

        if ($total >= $this->limit) {
            $to_delete = array_splice($files_cur, 0, ($total - $this->limit));

            foreach($to_delete as $file) {
                unlink("{$this->db_path}{$file}");
            }
        }
    }

    public function createBackup()
    {
        $dbname = DB_NAME;
        $dbhost = DB_HOST;
        $dbuser = DB_USER;
        $dbpass = DB_PASSWORD;

        $date = date("Y-md-His");

        $options = get_option('tnwpt_backupdb');

        if ($options && isset($options['limit']) && $options['limit'] > 0) {
            $this->limit = (int)$options['limit'];
        }

        $backup_file = "{$this->db_path}{$dbname}-{$date}.sql.gz";

        $dump = new IMysqldump\Mysqldump("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass, [
            'compress' => 'Gzip'
        ]);

        $dump->start($backup_file);

        $this->checkFilesLimit($dbname);
    }

    private function backupList()
    {
        $html = '<div class="backup-list"><ul>';

        $files = array_values(array_diff(scandir($this->db_path), ['..', '.'])); // files in dir

        $nonce = wp_create_nonce($_ENV['MAIL_NONCE']);

        if ($files) {
            foreach($files as $file) {
                $html .= "<li>{$file} <a href='{$this->db_path_url}{$file}'>Скачать</a> <button type='button' class='js-backup-delete' data-file='{$file}' data-token='{$nonce}'><span class='dashicons dashicons-trash'></span></button></li>";
            }
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

        $path = "{$this->db_path}{$_POST['file']}";

        unlink($path);

        wp_send_json_success();

        die();
    }
}
