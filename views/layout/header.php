<?php
// File Security Check
if (!defined('ABSPATH')) exit;

use tnwpt\helpers\View;
?>

<header class="header">
    <div class="v-box">
        <p>This is "views/header"</p>

        <div class="v-row">
            <div class="v-col v-col-1">
                <?= View::getLogo('img','',true); ?>
            </div>
            <div class="v-col v-col-11">
                <nav class="" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
                    <?php
                        wp_nav_menu([
                            'theme_location' => 'place-menu-header',
                            'container' => false,
                            'menu_class' => 'v-ul-clear',
                            'items_wrap' => '<ul id="%1$s" class="%2$s" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">%3$s</ul>'
                        ]);
                    ?>
                </nav>
            </div>
        </div>

    </div>
</header>
<!-- .HEADER -->
