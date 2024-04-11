<?php
// File Security Check
if (!defined('ABSPATH')) exit;

use tnwpt\helpers\View;
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <div class="v-box">
        <p>This is "front-page"</p>

        <?//= View::breadcrumbs(); ?>

        <nav class="" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
            <?php /*/
                wp_nav_menu([
                    'theme_location' => 'place-menu-header',
                    'container' => false,
                    'menu_class' => 'v-ul-clear',
                    'items_wrap' => '<ul id="%1$s" class="%2$s" itemprop="about" itemscope="" itemtype="http://schema.org/ItemList">%3$s</ul>'
                ]);
            /**/ ?>
        </nav>

        <?php //get_search_form(); ?>

        <form action="/" data-bouncer class="v-form">
            <input type="text" name="mouse" value="" class="v-d-none">
            <input type="hidden" name="token" value="<?= wp_create_nonce($_ENV['MAIL_NONCE']); ?>">
            <input type="hidden" name="title" value="<?= wp_get_document_title(); ?>">
            <input type="hidden" name="url" value="<?= get_self_link(); ?>">
            <input type="hidden" name="sbj" value="<?= wp_get_document_title(); ?>">

            Инпут:
            <span class="v-fg v-d-inline-block">
                <input type="text" name="name" required class="v-input v-d-inline-block">
            </span>

            <div class="v-cbx is-cbx v-fg v-d-block">
                <label class="v-cbx-wrapper v-d-flex v-ai-center v-flex-nowrap">
                    <input type="checkbox" name="type[]" value="checkbox">
                    <span class="v-cbx-box v-icon v-d-flex v-ai-center v-jc-center v-mr-16"></span>
                    <span class="v-cbx-txt">checkbox</span>
                </label>
            </div>

            <div class="v-cbx is-radio v-fg is-cbx v-d-block">
                <label class="v-cbx-wrapper v-d-flex v-ai-center v-flex-nowrap">
                    <input type="radio" name="type[]" value="radio" checked>
                    <span class="v-cbx-box v-icon v-d-flex v-ai-center v-jc-center v-mr-16"></span>
                    <span class="v-cbx-txt">radio</span>
                </label>
            </div>

            <div class="v-form-result-success v-status-success v-ta-center v-mb-24 js-result-success">
                Спасибо! Ваша заявка отправлена
            </div>

            <div class="v-form-result-error v-status-error v-ta-center v-mb-24 js-result-error">
                Ошибка! Что-то пошло не так
            </div>

            <button type="submit" name="button">Отправить</button>
        </form>
    </div>

<?php endwhile; endif; ?>
