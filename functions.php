<?php

function debug($var) {
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php') ):
  require_once dirname( __FILE__ ) . '/vendor/autoload.php';
endif;

if ( class_exists( 'tnwpt\\Init' ) ):
  tnwpt\Init::register_services();
endif;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
?>
