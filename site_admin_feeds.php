<?php
include_once("bootstrap.inc.php");

if (!@$_SESSION["userID"])
{
  header("Location: ".ROOT_URL."login");
  exit();
}

if (!$isAdmin)
{
  header("Location: ".ROOT_URL);
  exit();
}

if(@$_POST["addUrl"])
{
  SQLLib::InsertRow("feeds",array("url"=>$_POST["addUrl"]));
  header("Location: ".ROOT_URL."admin/feeds");
  exit();
}

if(@$_POST["deleteID"])
{
  SQLLib::Query(sprintf_esc("DELETE FROM entries WHERE sourceFeedID=%d",$_POST["deleteID"])); // ?
  SQLLib::Query(sprintf_esc("DELETE FROM feeds WHERE id=%d",$_POST["deleteID"]));
  header("Location: ".ROOT_URL."admin/feeds");
  exit();
}

$IITLE = "admin";
include_once("header.inc.php");

$items = SQLLib::SelectRows("SELECT * FROM feeds");
echo "<ul>\n";
foreach($items as $item)
{
  printf("  <li><form method='post'><input type='hidden' name='deleteID' value='%d'/><input type='submit' value='Delete'/></form><a href='%s'>%s</a></li>\n",$item->id,_html($item->url),_html($item->url));
}
echo "</ul>\n";

echo "<form method='post'><input type='url' name='addUrl'/><input type='submit' value='Add'/></form>";

include_once("footer.inc.php");
?>