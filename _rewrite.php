<?php
include_once("rewriter.inc.php");

$rewriter = new Rewriter();
$rewriter->addRules(array(
  "^\/+news\/([0-9]+).*$" => "index.php?id=$1",

  "^\/+login\/?$" => "login.php",
  "^\/+logout\/?$" => "logout.php",
  
  "^\/+submit\/?$" => "site_submit.php",

  "^\/+feeds\/rss\/?$" => "feed.php?format=rss",
  "^\/+feeds\/atom\/?$" => "feed.php?format=atom",
  "^\/+feeds\/json\/?$" => "feed.php?format=json",

  "^\/+admin\/?$" => "site_admin.php",
  "^\/+admin\/entries\/?$" => "site_admin_entries.php",
  "^\/+admin\/feeds\/?$" => "site_admin_feeds.php",
));
$rewriter->rewrite();
?>