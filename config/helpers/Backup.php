<?php
namespace tnwpt\helpers;

class Backup
{
    public function register()
    {
        // -- Add cron job 'tnwpt_backup_cron'
        add_action('tnwpt_backup_cron', [&$this, 'action_backup_cron']);
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

        unlink(ABSPATH . '/site.zip');

        $zip = new \ZipArchive();
        $zip->open(ABSPATH . '/site.zip', \ZipArchive::CREATE|\ZipArchive::OVERWRITE);

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
}
