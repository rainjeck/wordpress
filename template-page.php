<?php
// File Security Check
if (!defined( 'ABSPATH' )) exit;

/*
  Template Name: Template
  Template Post Type: page
*/
?>

<?php get_header(); ?>

<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

    <?php get_template_part('views/page/template', 'page'); ?>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
