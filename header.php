<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

$app = get_query_var('theme_options');
$codes = (isset($app['group_code'])) ? array_shift($app['group_code']) : [];
?>

<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>

	<?php
  if (!is_user_logged_in()) {
    echo (!empty($codes['head_in'])) ? $codes['head_in'] : '';
  }
  ?>

</head>

<body>

	<?php
  if (!is_user_logged_in()) {
    echo (!empty($codes['body_start'])) ? $codes['body_start'] : '';
  }
  ?>

  <div class="wrapper">

		<?php get_template_part('views/layout/header'); ?>

		<main class="main">
