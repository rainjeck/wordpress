<?php

// File Security Check
if (!defined('ABSPATH')) {
  exit;
}

use tnwpt\helpers\View;

$app = get_query_var('app');
?>

<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>

    <?php
        if (!is_user_logged_in()) {
            echo (View::checkArray($app, 'head')) ? $app['head'] : '';
        }
    ?>

</head>

<body>

    <?php
        if (!is_user_logged_in()) {
            echo (View::checkArray($app, 'bodystart')) ? $app['bodystart'] : '';
        }
    ?>

    <div class="wrapper">

        <?php get_template_part('views/layout/header'); ?>

        <main class="main">
