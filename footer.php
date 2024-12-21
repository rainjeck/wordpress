<?php
// File Security Check
if (!defined('ABSPATH')) exit;

use tnwpt\helpers\View;

$app = get_query_var('app');

$bodyend = View::checkMeta($app, 'bodyend', '');
$bodyend = ($bodyend) ? preg_replace('/\s\s+/', ' ', $bodyend) : '';
?>

        <?php get_template_part('views/layout/footer'); ?>

        <?= ( !is_user_logged_in() ) ? $bodyend : ''; ?>

        <?php wp_footer(); ?>

    </body>
</html>
