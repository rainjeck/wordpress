<?php
use tnwpt\helpers\View;

$data = View::checkMeta($args, 'data', '');
?>

<?php include(locate_template('views/email/parts/header.php')); ?>

<table class="container">
    <tr>
        <td class="content">
            <?php if ( View::checkArray($data,'title','not_empty') ): ?>
                <p> <strong>Страница</strong>: <a href="<?= $data['url']; ?>"><?= $data['title']; ?></a> </p>
                <p>&nbsp;</p>
            <?php endif; ?>

            <?php if ( View::checkArray($data,'subject','not_empty') ): ?>
                <p> <strong>Запрос</strong>: <?= $data['subject']; ?> </p>
            <?php endif; ?>

            <?php if ( View::checkArray($data,'name','not_empty') ): ?>
                <p> <strong>Имя</strong>: <?= $data['name']; ?> </p>
            <?php endif; ?>

            <?php if ( View::checkArray($data,'tel','not_empty') ): ?>
                <p> <strong>Телефон</strong>: <?= $data['tel']; ?> </p>
            <?php endif; ?>

            <?php if ( View::checkArray($data,'email','not_empty') ): ?>
                <p> <strong>E-mail</strong>: <?= $data['email']; ?> </p>
            <?php endif; ?>

            <?php if ( View::checkArray($data,'msg','not_empty') ): ?>
                <p> <strong>Сообщение</strong>: <?= $data['msg']; ?> </p>
            <?php endif; ?>
        </td>
    </tr>
</table>

<?php include(locate_template('views/email/parts/footer.php')); ?>
