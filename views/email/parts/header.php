<?php
use tnwpt\helpers\View;

$logo_id = get_theme_mod('custom_logo');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">

    <title>Email</title>

    <style>
      #outlook a { padding: 0; }
      html { min-height: 100%; background-color: #f3f3f3; }
      body { width: 100% !important; min-width: 100%; box-sizing: border-box; margin: 0; Margin: 0; padding: 0; }
      table { border-spacing: 0; border-collapse: collapse; }
      table, tr, td { padding: 0; vertical-align: top; text-align: left; }
      td { word-wrap: break-word; border-collapse: collapse !important; }
      table.body { background: #f3f3f3; height: 100%; width: 100%; }
      body, table.body, h1, h2, h3, h4, h5, h6, p, td, th, a { color: #0a0a0a; font-family: Ubuntu, Helvetica, Arial, sans-serif; font-weight: normal; padding: 0; text-align: left; Margin: 0; margin: 0; }
      body, table.body, p, td, th { font-size: 16px; line-height: 1.6; }
      table.container { width: 580px; margin: 0 auto; Margin: 0 auto; text-align: inherit; }
      table.table { width: 100%; margin: 0 auto; Margin: 0 auto; text-align: inherit; }

      .logo { text-align: center; padding: 15px 0; }
      .logo-link { display: inline-block; }
      .logo-img { width: 160px; }

      .footer { text-align: center; padding-top: 20px; padding-right: 10px; padding-bottom: 10px; border-left: 10px; font-size: 12px; }

      .content { padding-left: 15px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px; background-color: #fff; }

      .space { padding-bottom: 16px; }
      .space-half { padding-bottom: 8px; }
      .space-third { padding-bottom: 4px; }

      .line { border-width: 1px 0; border-color: #f3f3f3; border-style: solid; }
    </style>

  </head>

  <body>

    <table class="body">
      <tbody>
        <tr>
          <td>

            <table class="container">
              <tr>
                <td class="logo">
                  <?php /* ?>
                  <a href="<?= home_url(); ?>" class="logo-link" target="_blank">
                    <img src="<?= wp_get_attachment_image_url($logo_id, 'small'); ?>" alt="<?= bloginfo('name'); ?>" class="logo-img">
                  </a>
                  <?php /**/ ?>
                </td>
              </tr>
            </table>
