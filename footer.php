<?php
// File Security Check
if (!defined('ABSPATH')) exit;

use tnwpt\helpers\View;

$app = get_query_var('app');
?>

        <?php get_template_part('views/layout/footer'); ?>

        <?php
            if ( !is_user_logged_in() ) {
                echo View::checkMeta($app, 'bodyend', '');
            }
        ?>

        <?php wp_footer(); ?>

    </body>
</html>
