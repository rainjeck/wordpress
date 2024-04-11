<?php
// File Security Check
if (!defined('ABSPATH')) exit;

use tnwpt\helpers\View;

$app = get_query_var('app');
?>

            </main>
            <!-- end .main -->

        <?php get_template_part('views/layout/footer'); ?>

        </div>
        <!-- end .wrapper -->

        <?php
            if (!is_user_logged_in()) {
                echo View::checkArray($app, 'bodyend') ? $app['bodyend'] : '';
            }
        ?>

        <?php wp_footer(); ?>

    </body>

</html>
