<?php
// File Security Check
if (!defined('ABSPATH')) exit;
?>

<main class="main">
    <p>This is "search"</p>

    <p>Вы искали: <strong><?php echo get_search_query(); ?></strong></p>

    <?php if (have_posts()): ?>
        <?php while (have_posts()): the_post(); ?>

            <div>
                <h2><?php the_title(); ?></h2>

                <?php the_excerpt(); ?>
            </div>

        <?php endwhile; ?>

        <?php the_posts_navigation(); ?>
        <?php the_posts_pagination(); ?>

    <?php else : ?>

        <p>По вашему запросу ничего не найдено.</p>

    <?php endif; ?>
</main>
