<?php

namespace tnwpt;

class Init
{
    public static function get_services()
    {
        return [
            setup\Setup::class,
            custom\PostType::class,
            custom\AdminOptionsPage::class,
            custom\CustomFields::class,
            ajax\Mail::class,
            helpers\BackupDB::class,
            helpers\Admin::class,
            helpers\AdminColumns::class,
            helpers\ImageOptimizer::class,
            helpers\Filter::class,
            helpers\View::class,
        ];
    }

    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);

            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    private static function instantiate($class)
    {
        return new $class();
    }
}

?>
