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
    </div>

    <?php /* ?>

    <?php get_search_form(); ?>

    <?php /**/ ?>

    <form action="/" data-bouncer class="ui-form">
      <input type="text" name="mouse" value="" style="display: none;">
      <input type="hidden" name="token" value="<?= wp_create_nonce($_ENV['MAIL_NONCE']); ?>">
      <input type="hidden" name="title" value="<?= wp_get_document_title(); ?>">
      <input type="hidden" name="url" value="<?= get_self_link(); ?>">
      <input type="hidden" name="sbj" value="<?= wp_get_document_title(); ?>">

      Инпут:
      <span class="ui-fg ui-d-inline-block"><input type="text" name="name" required class="ui-input ui-d-inline-block"></span>

      <button type="submit" name="button">Отправить</button>
    </form>




<?php endwhile;
endif; ?>
