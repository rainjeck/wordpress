<?php
// File Security Check
if (!defined('ABSPATH')) exit;
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <p>This is "single"</p>

    <h1><?php the_title(); ?></h1>

    <?php the_content(); ?>

    <?php if (comments_open()): comments_template(); endif; ?>

<?php endwhile; endif; ?>
