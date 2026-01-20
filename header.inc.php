<!DOCTYPE html>
<html>
<head>
  <title>Scene.org News<?=($TITLE?" :: "._html($TITLE):"")?></title>
  <link rel="alternate" href="<?=ROOT_URL?>feeds/rss/" type="application/rss+xml" title="News feed">
</head>
<body>

<h1>Scene.org Demoscene News Service</h1>

<header>
  <nav>
    <ul>
      <li><a href="<?=ROOT_URL?>">Latest news</a></li>
      <li><a href="<?=ROOT_URL?>submit/">Submit news</a></li>
      <li><a href="<?=ROOT_URL?>feeds/rss/">RSS</a></li>
      <li><a href="<?=ROOT_URL?>feeds/json/">JSON</a></li>
    </ul>
<?php if ($isAdmin) {?>
    <ul id='adminMenu'>
      <li>Admin:</li>
      <li><a href="<?=ROOT_URL?>admin/">Approval queue</a></li>
      <li><a href="<?=ROOT_URL?>admin/entries/">All items</a></li>
      <li><a href="<?=ROOT_URL?>admin/feeds/">Feeds</a></li>
    </ul>
<?php }?>
  </nav>
</header>

<main>