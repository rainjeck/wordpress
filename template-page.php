<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
  Template Name: Template
  Template Post Type: page
*/
?>

<?php get_header(); ?>

<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

    <p>This is "template page"</p>

    <h1><?php the_title(); ?></h1>

    <?php the_content(); ?>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
