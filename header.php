<?php

// File Security Check
if (!defined('ABSPATH')) {
  exit;
}

use tnwpt\helpers\View;

$app = get_query_var('app');
$codes = (isset($app['g_code'])) ? array_shift($app['g_code']) : [];
?>

<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta http-equiv="content-type" content="text/html">
  <meta http-equiv='X-UA-Compatible', content="IE=edge">
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE">
  <meta name="format-detection" content="telephone=no">

  <?php wp_head(); ?>

  <?php
  if (!is_user_logged_in()) {
    echo (View::checkArray($codes, 'head_in')) ? $codes['head_in'] : '';
  }
  ?>

</head>

<body>

  <?php
  if (!is_user_logged_in()) {
    echo (View::checkArray($codes, 'body_start')) ? $codes['body_start'] : '';
  }
  ?>

  <div class="wrapper">

    <?php get_template_part('views/layout/header'); ?>

    <main class="main">
