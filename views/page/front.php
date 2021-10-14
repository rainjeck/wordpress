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

    <form data-bouncer action="/">
      <input type="text" name="mouse" value="" style="display: none;">
      <input type="hidden" name="token" value="<?= wp_create_nonce($_ENV['MAIL_NONCE']); ?>">
      <input type="hidden" name="post" value="<?= $post->ID; ?>">

      Инпут: <input type="text" name="name" required>
      <button type="submit" name="button">Отправить</button>
    </form>

    <?php /**/ ?>


<?php endwhile;
endif; ?>
