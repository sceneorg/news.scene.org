<!DOCTYPE html>
<html>
<head>
  <title>Scene.org News<?=($TITLE?" :: "._html($TITLE):"")?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="alternate" href="<?=ROOT_URL?>feeds/rss/" type="application/rss+xml" title="News feed (RSS)" />
  <link rel="alternate" href="<?=ROOT_URL?>feeds/atom/" type="application/atom+xml" title="News feed (Atom)" />
  <link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>style.css?1747393803" media="screen" />
  <link rel="alternate icon" href="<?=ROOT_URL?>favicon.png" type="image/png" />
  <meta name="theme-color" content="#ff7a00" />
<?php
  if ($metaValues) foreach ($metaValues as $k=>$v)
  {
    printf("  <meta property=\"%s\" content=\"%s\"/>\n",$k,_html($v));
  }
?>
</head>
<body class="<?=_html($BODYCLASS)?>">

<h1>Scene.org Demoscene News Service</h1>

<header>
  <nav>
    <ul>
      <li><a href="<?=ROOT_URL?>">Latest news</a></li>
      <li><a href="<?=ROOT_URL?>submit/">Submit news</a></li>
      <li><a href="<?=ROOT_URL?>feeds/rss/">RSS</a></li>
      <li><a href="<?=ROOT_URL?>feeds/atom/">Atom</a></li>
      <li><a href="<?=ROOT_URL?>feeds/json/">JSON</a></li>
    </ul>
<?php if ($isAdmin) {
  $c = SQLLib::SelectRow("SELECT COUNT(*) AS c FROM entries WHERE status='pending'")->c;
  ?>
    <ul id='adminMenu'>
      <li>Admin:</li>
      <li><a href="<?=ROOT_URL?>admin/">Approval queue<?=($c?sprintf("<span class='pendingCount'>%d</span>",$c):"")?></a></li>
      <li><a href="<?=ROOT_URL?>admin/entries/">All items</a></li>
      <li><a href="<?=ROOT_URL?>admin/feeds/">Feeds</a></li>
    </ul>
<?php }?>
  </nav>
</header>

<main>