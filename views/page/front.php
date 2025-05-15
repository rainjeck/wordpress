<?php
// File Security Check
if (!defined('ABSPATH')) exit;

use tnwpt\helpers\View;
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<main class="main">
    <div class="v-box">
        <?= View::breadcrumbs('v-ul-clear v-d-flex v-ai-center'); ?>

        <p>content</p>

        <form action="#" data-action="mail" data-bouncer class="v-form" data-token="<?= wp_create_nonce($_ENV['MAIL_NONCE']); ?>">
            <?= View::getFormFields(); ?>

            <span class="v-fg">
                <input type="text" name="name" required placeholder="E-mail" class="v-input v-w-100">
            </span>

            <div class="v-fg">
                <input type="tel" name="tel" class="v-input v-w-100 js-masked" required placeholder="Ваш номер" data-mask="+7 (000) 000-00-00">
            </div>

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
</main>

<?php endwhile; endif; ?>
