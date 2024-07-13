<?php
// File Security Check
if (!defined( 'ABSPATH' )) exit;
?>

<main class="main">
    <p>This is "template page"</p>

    <h1><?php the_title(); ?></h1>

    <?php the_content(); ?>
</main>
