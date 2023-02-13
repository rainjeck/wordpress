<?php
// File Security Check
if (!defined('ABSPATH')) {
  exit;
}
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <div class="container">
    <p>This is "page"</p>

    <h1><?php the_title(); ?></h1>

    <?php the_content(); ?>
  </div>

<?php endwhile; endif; ?>
