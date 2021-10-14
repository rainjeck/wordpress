<?php

namespace tnwpt\setup;

class Enqueue
{
  public function register()
  {
    add_action( 'wp_enqueue_scripts', [&$this, 'enqueueScripts'] );
  }

  public function enqueueScripts()
  {
    $dev = $_ENV['APPDEV'];
    $url = get_template_directory_uri();

    wp_deregister_style( 'wp-block-library' );

    if ($dev) {
      wp_enqueue_style( 'main', "{$url}/assets/css/libs.min.css", array(), null, 'all' );
      wp_enqueue_style( 'app', "{$url}/assets/css/main.css", array('main'), null, 'all' );

      wp_enqueue_script( 'main', "{$url}/assets/js/libs.min.js", array(), null, true );
      wp_enqueue_script( 'app', "{$url}/assets/js/main.js", array('main'), null, true );
    }

    if (!$dev) {
      wp_enqueue_style( 'app', "{$url}/assets/css/bundle.min.css", array(), time(), 'all' );
      wp_enqueue_script( 'app', "{$url}/assets/js/bundle.min.js", array(), time(), true );
    }

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
      wp_enqueue_script( 'comment-reply' );
    }
  }
}

?>
