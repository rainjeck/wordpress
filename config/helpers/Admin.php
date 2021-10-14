<?php

namespace tnwpt\helpers;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

class Admin
{
  public function register()
  {
    /**
    * Отключаем принудительную проверку новых версий WP, плагинов и темы в админке,
    * чтобы она не тормозила, когда долго не заходил и зашел...
    * Все проверки будут происходить незаметно через крон или при заходе на страницу: "Консоль > Обновления".
    */

    if ( is_admin() ) {
      // отключим проверку обновлений при любом заходе в админку...
      // remove_action( 'admin_init', '_maybe_update_core' );
      remove_action( 'admin_init', '_maybe_update_plugins' );
      remove_action( 'admin_init', '_maybe_update_themes' );

      // отключим проверку обновлений при заходе на специальную страницу в админке...
      // remove_action( 'load-plugins.php', 'wp_update_plugins' );
      remove_action( 'load-themes.php', 'wp_update_themes' );

      /**
       * отключим проверку необходимости обновить браузер в консоли - мы всегда юзаем топовые браузеры!
       * эта проверка происходит раз в неделю...
       * @see https://wp-kama.ru/function/wp_check_browser_version
       */
      add_filter( 'pre_site_transient_browser_'. md5( $_SERVER['HTTP_USER_AGENT'] ), '__return_true' );
    }

    add_action( 'admin_enqueue_scripts', [ &$this, 'adminEnqueueScripts' ] );
    add_action( 'login_enqueue_scripts', [ &$this, 'loginEnqueueScripts' ] );

    add_action( 'wp_before_admin_bar_render', [ &$this, 'beforeAdminBarRender' ] );

    add_filter( 'login_headertext', [ &$this, 'loginHeaderText' ], 10, 1 );
    add_filter( 'login_headerurl', [ &$this, 'loginHeaderUrl' ], 10, 1 );
  }

  public function adminEnqueueScripts()
  {
    $url = get_template_directory_uri();

    wp_enqueue_style('admin-modify', "{$url}/assets/css/admin.css", array('cmb2-styles'), null, 'all');
    wp_enqueue_script( 'admin-main', "{$url}/assets/js/admin.min.js", array(), null, true );
  }

  public function loginEnqueueScripts()
  {
    $url = get_template_directory_uri();

    wp_enqueue_style('tnwpt_login', "{$url}/assets/css/admin.css", array(), null, 'all');
  }

  public function beforeAdminBarRender()
  {
    global $wp_admin_bar;

    // Don't show for logged out users.
    if ( ! is_user_logged_in() ) return;

    // Show only when the user is a member of this site, or they're a super admin.
    if ( ! is_user_member_of_blog() && ! current_user_can( 'manage_network' ) ) return;

    $wp_admin_bar->add_menu( [
      'id'    => 'site-name',
      'title' => __('Visit Site'),
      'href'  => ( is_admin() || ! current_user_can( 'read' ) ) ? home_url( '/' ) : admin_url(),
      'meta' => [ 'target' => '_blank', 'title' => 'Open in a new tab' ]
    ] );

    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('view-site');
    $wp_admin_bar->remove_node('user-info');
    $wp_admin_bar->remove_node('edit-profile');

    return $wp_admin_bar;
  }

  public function loginHeaderText()
  {
    $html = '';

    $html = home_url();

    // $logo_id = get_theme_mod( 'custom_logo' );

    // if ( $logo_id ) {
    //   $logo_url = wp_get_attachment_image_url( $logo_id, 'medium' );
    //   $html = "<img src='{$logo_url}'/>";
    // } else {
    //   $html = get_bloginfo( 'name' );
    // }

    return $html;
  }

  public function loginHeaderUrl()
  {
    return home_url();
  }
}

?>
