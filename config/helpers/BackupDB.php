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

    add_action('admin_head', [&$this, 'setupCron']);
    add_action('tnwpt_backupdb_cron', [&$this, 'actionCron']);

    add_action('cmb2_admin_init', [&$this, 'adminControlPage']);
  }

  public function checkBackupDir()
  {
    if (!is_dir($this->db_path)) {
      mkdir($this->db_path);
    }
  }

  public function setupCron()
  {
    if (!wp_next_scheduled('tnwpt_backupdb_cron')) {
      wp_schedule_event(time(), 'weekly', 'tnwpt_backupdb_cron');
    }
  }

  public function actionCron()
  {
    $this->createBackup();
  }

  public function adminControlPage()
  {
    $list = $this->backupList();

    $mb = new_cmb2_box([
      'id' => 'backup',
      'title' => 'Backup DB',
      'object_types' => [ 'options-page' ],
      'parent_slug' => 'tools.php',
      'option_key' => 'tnwpt_backupdb',
    ]);

    $mb->add_field([
      'id' => 'backupdb_title',
      'name' => '',
      'desc' => "
        <p>Папка, где хранятся копии: {$this->db_path}</p>
        <p>Расписание устанавливается через плагин <a href='/wp-admin/tools.php?page=crontrol_admin_manage_page'>WP Crontrol</a>. Задание '<strong>tnwpt_backupdb_cron</strong>'</p>
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

    return;
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
    $html = '';

    $files = array_values(array_diff(scandir($this->db_path), ['..', '.'])); // files in dir

    if ($files) {
      foreach($files as $file) {
        $html .= "<p>{$file} <a href='{$this->db_path_url}{$file}'>Скачать</a></p>";
      }
    }

    return $html;
  }
}
?>
