<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<!doctype html>
<html lang="en" dir="<?php echo $siteLanguage['language_direction']; ?>">

<head>
    <!-- Basic Page Needs ======================== -->
    <meta charset="utf-8">
    <?php echo $this->writeMetaTags(); ?>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0,user-scalable=0" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/<?php echo CONF_ZOOM_VERSION ?>/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/<?php echo CONF_ZOOM_VERSION ?>/css/react-select.css" />
</head>

<body>