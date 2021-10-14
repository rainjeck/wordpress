<?php
// File Security Check
if (!defined('ABSPATH')) {
  exit;
}
?>

<?php get_header(); ?>

<?php
if (is_404()) get_template_part('views/layout/404');

if (is_search()) get_template_part('views/page/search');

if (is_front_page()) get_template_part('views/page/front');

if (!is_front_page()) {
  if (is_singular('page')) get_template_part('views/page/page');
}

if (is_singular('post')) get_template_part('views/single/single');

if (is_archive()) get_template_part('views/taxonomy/archive');
?>

<?php get_footer(); ?>
