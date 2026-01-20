<?php
include_once("rewriter.inc.php");

$rewriter = new Rewriter();
$rewriter->addRules(array(
  "^\/+login\/?$" => "login.php",
  "^\/+logout\/?$" => "logout.php",
  
  "^\/+submit\/?$" => "site_submit.php",

  "^\/+feeds\/rss\/?$" => "feed.php?format=rss",
  "^\/+feeds\/json\/?$" => "feed.php?format=json",

  "^\/+admin\/?$" => "site_admin.php",
  "^\/+admin\/entries\/?$" => "site_admin_entries.php",
  "^\/+admin\/feeds\/?$" => "site_admin_feeds.php",
));
$rewriter->rewrite();
?>