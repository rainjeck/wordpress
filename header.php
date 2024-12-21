<?php
// File Security Check
if (!defined('ABSPATH')) exit;

use tnwpt\helpers\View;

$app = get_query_var('app');

$inhead = View::checkMeta($app, 'head', '');
$inhead = ($inhead) ? preg_replace('/\s\s+/', ' ', $inhead) : '';
$inbody = View::checkMeta($app, 'bodystart', '');
$inbody = ($inbody) ? preg_replace('/\s\s+/', ' ', $inbody) : '';
?>

<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?= ( !is_user_logged_in() ) ? $inhead : ''; ?>

    <?php wp_head(); ?>
</head>

<body>
    <?= ( !is_user_logged_in() ) ? $inbody : ''; ?>

    <?php get_template_part('views/layout/header'); ?>
