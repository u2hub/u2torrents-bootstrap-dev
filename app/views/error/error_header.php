<?php
// Micro Time
$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="<?php echo DB_CHAR; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="M-jay" />
    <meta name="generator" content="TorrentTrader <?php echo VERSION; ?>" />
    <meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.xyx for the support forums. " />
    <meta name="keywords" content="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" />
    <title><?php echo $title; ?></title>
  
    <!-- Bootstrap & core CSS -->
    <link href="<?php echo URLROOT; ?>/assets/themes/<?php echo ($_SESSION['stylesheet'] ?: DEFAULTTHEME) ?>/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URLROOT; ?>/assets/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- TT Custom CSS, any edits must go here-->
    <link href="<?php echo URLROOT; ?>/assets/themes/<?php echo ($_SESSION['stylesheet'] ?: DEFAULTTHEME) ?>/customstyle.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/monokai-sublime.min.css">
  </head>
<body>
  
  <div class="container-fluid" style="padding-top: 10px;">

  <div class="col-sm-12">
  <?php require APPROOT . '/views/inc/default/navbar.php'; ?>
  </div>

<table class="table">
<tr>
<th style="width: 70%">