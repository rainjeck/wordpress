<?php
// File Security Check
if (!defined('ABSPATH')) {
  exit;
}

use tnwpt\helpers\View;
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <div class="container">
    <p>This is "front-page"</p>

    <?//= View::breadcrumbs(); ?>

    <nav class="container" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
      <?php /*/
      wp_nav_menu([
        'theme_location' => 'place-menu-header',
        'container' => false,
        'menu_class' => 'ui-ul-clear',
        'items_wrap' => '<ul id="%1$s" class="%2$s" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">%3$s</ul>'
      ]);
      /**/ ?>
    </nav>

    <?php //get_search_form(); ?>

    <form action="/" data-bouncer class="ui-form">
      <input type="text" name="mouse" value="" class="ui-d-none">
      <input type="hidden" name="token" value="<?= wp_create_nonce($_ENV['MAIL_NONCE']); ?>">
      <input type="hidden" name="title" value="<?= wp_get_document_title(); ?>">
      <input type="hidden" name="url" value="<?= get_self_link(); ?>">
      <input type="hidden" name="sbj" value="<?= wp_get_document_title(); ?>">

      Инпут:
      <span class="ui-fg ui-d-inline-block"><input type="text" name="name" required class="ui-input ui-d-inline-block"></span>

      <div class="ui-cbx ui-fg is-cbx ui-d-block">
        <label class="ui-cbx-wrapper ui-d-flex ui-ai-center ui-flex-nowrap">
          <input type="checkbox" name="type[]" value="checkbox">
          <span class="ui-cbx-box ui-icon ui-d-flex ui-ai-center ui-jc-center ui-mr-16"></span>
          <span class="ui-cbx-txt">checkbox</span>
        </label>
      </div>

      <div class="ui-cbx is-radio ui-fg is-cbx ui-d-block">
        <label class="ui-cbx-wrapper ui-d-flex ui-ai-center ui-flex-nowrap">
          <input type="radio" name="type[]" value="radio" checked>
          <span class="ui-cbx-box ui-icon ui-d-flex ui-ai-center ui-jc-center ui-mr-16"></span>
          <span class="ui-cbx-txt">radio</span>
        </label>
      </div>

      <div class="ui-form-result is-success ui-status-success ui-ta-center ui-mb-24 js-result-success">Спасибо! Ваша заявка отправлена</div>
      <div class="ui-form-result is-error ui-status-error ui-ta-center ui-mb-24 js-result-error">Ошибка! Что-то пошло не так</div>

      <button type="submit" name="button">Отправить</button>
    </form>
  </div>

<?php endwhile; endif; ?>
