<?php

$name = (isset($data['name']) && !empty($data['name'])) ? sanitize_text_field($data['name']) : '';
$tel = (isset($data['tel']) && !empty($data['tel'])) ? sanitize_text_field($data['tel']) : '';
$email = (isset($data['email']) && !empty($data['email'])) ? sanitize_email($data['email']) : '';
$msg = (isset($data['msg']) && !empty($data['msg'])) ? sanitize_textarea_field($data['msg']) : '';
$subject = (isset($data['sbj']) && !empty($data['sbj'])) ? sanitize_textarea_field($data['sbj']) : '';

$post = '';
if (isset($data['post']) && !empty($data['post'])) {
  $post_id = (int)sanitize_text_field($data['post']);
  $post .= '<a href="' . get_permalink($post_id) . '">' . get_post_field('post_title', $post_id) . '</a>';
}
?>

<?php include(locate_template('views/email/parts/header.php')); ?>

<table class="container">
  <tr>
    <td class="content">
      <?php if ($post) : ?>
        <p> <strong>Страница</strong>: <?= $post; ?> </p>
        <p>&nbsp;</p>
      <?php endif; ?>

      <?php if ($subject) : ?>
        <p> <strong>Тема</strong>: <?= $subject; ?> </p>
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
