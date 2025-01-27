<?php
use tnwpt\helpers\View;

$data = View::checkMeta($args, 'data', '');
?>

<?php include(locate_template('views/email/parts/header.php')); ?>

<?php if ( View::checkArray($data,'title','_') ): ?>
    <p> <strong>Страница</strong>: <a href="<?= $data['url']; ?>"><?= $data['title']; ?></a> </p>
    <p>&nbsp;</p>
<?php endif; ?>

<?php if ( View::checkArray($data,'subject','_') ): ?>
    <p> <strong>Запрос</strong>: <?= $data['subject']; ?> </p>
<?php endif; ?>

<?php if ( View::checkArray($data,'name','_') ): ?>
    <p> <strong>Имя</strong>: <?= $data['name']; ?> </p>
<?php endif; ?>

<?php if ( View::checkArray($data,'tel','_') ): ?>
    <p> <strong>Телефон</strong>: <?= $data['tel']; ?> </p>
<?php endif; ?>

<?php if ( View::checkArray($data,'email','_') ): ?>
    <p> <strong>E-mail</strong>: <?= $data['email']; ?> </p>
<?php endif; ?>

<?php if ( View::checkArray($data,'msg','_') ): ?>
    <p> <strong>Сообщение</strong>: <?= $data['msg']; ?> </p>
<?php endif; ?>

<?php if ( View::checkArray($data,'utm','_') ): ?>
    <?php foreach($data['utm'] as $key => $one): ?>
        <br> <?= $key; ?>: <?= $one; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php include(locate_template('views/email/parts/footer.php')); ?>
