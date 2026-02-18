<?php
define("SQLLIB_SUPPRESSCONNECT", true);
include_once("bootstrap.inc.php");

switch($_GET["format"])
{
  case "json";
    header("Content-type: application/json");
    die(getFeedCacheJSON());
    break;
  case "rss";
    header("Content-type: application/rss+xml");
    die(getFeedCacheRSS());
    break;
  case "atom";
    header("Content-type: application/atom+xml");
    die(getFeedCacheAtom());
    break;
}