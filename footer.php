<?php
// File Security Check
if (!defined('ABSPATH')) {
  exit;
}

// use tnwpt\helpers\View;

$app = get_query_var('theme_options');
$codes = (isset($app['g_code'])) ? array_shift($app['g_code']) : [];
?>

</main>
<!-- end .main -->

<?php get_template_part('views/layout/footer'); ?>

</div>
<!-- end .wrapper -->

<?php
if (!is_user_logged_in()) {
  echo (!empty($codes['body_end'])) ? $codes['body_end'] : '';
}
?>

<?php wp_footer(); ?>

</body>

</html>