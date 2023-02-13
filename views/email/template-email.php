<?php
$name = (isset($data['name']) && !empty($data['name'])) ? sanitize_text_field($data['name']) : '';
$tel = (isset($data['tel']) && !empty($data['tel'])) ? sanitize_text_field($data['tel']) : '';
$email = (isset($data['email']) && !empty($data['email'])) ? sanitize_email($data['email']) : '';
$msg = (isset($data['msg']) && !empty($data['msg'])) ? sanitize_textarea_field($data['msg']) : '';
$subject = (isset($data['sbj']) && !empty($data['sbj'])) ? sanitize_textarea_field($data['sbj']) : '';
$title = (isset($data['title']) && !empty($data['title'])) ? sanitize_text_field($data['title']) : '';
$url = (isset($data['url']) && !empty($data['url'])) ? esc_url($data['url'], ['https', 'http']) : '';
?>

<?php include(locate_template('views/email/parts/header.php')); ?>

<table class="container">
  <tr>
    <td class="content">
    <?php if ($title) : ?>
        <p> <strong>Страница</strong>: <a href="<?= $url; ?>"><?= $title; ?></a> </p>
        <p>&nbsp;</p>
      <?php endif; ?>

      <?php if ($subject) : ?>
        <p> <strong>Запрос</strong>: <?= $subject; ?> </p>
      <?php endif; ?>

      <?php if ($name) : ?>
        <p> <strong>Имя</strong>: <?= $name; ?> </p>
      <?php endif; ?>

      <?php if ($tel) : ?>
        <p> <strong>Телефон</strong>: <?= $tel; ?> </p>
      <?php endif; ?>

      <?php if ($email) : ?>
        <p> <strong>E-mail</strong>: <?= $email; ?> </p>
      <?php endif; ?>

      <?php if ($msg) : ?>
        <p> <strong>Сообщение</strong>: <?= $msg; ?> </p>
      <?php endif; ?>
    </td>
  </tr>
</table>

<?php include(locate_template('views/email/parts/footer.php')); ?>
