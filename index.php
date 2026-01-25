<?php
include_once("bootstrap.inc.php");
include_once("header.inc.php");

$page = isset($_GET["p"]) ? ((int)$_GET["p"] - 1) : 0;
$perPage = 25;

$items = array();
$total = 0;
if (isset($_GET["id"]))
{
  $items = SQLLib::SelectRows(sprintf_esc("SELECT * FROM entries WHERE id=%d",$_GET["id"]));
}
else
{
  retrieveFeed($items, $total, $page, $perPage);
}

foreach($items as $item)
{
  echo "<article>\n";
  printf("  <h2><a href='%s'>%s</a></h2>\n",_html(getNewsUrl($item)),_html($item->title));
  printf("  <div>%s</div>\n",processPost($item->contents));
  if ($isAdmin)
  {
    printf("  <small><a href='".ROOT_URL."admin/entries/?id=%d'>edit</a></small>\n",$item->id);
  }
  echo "</article>\n";
}

if ($total > 1)
{
  paginator($total, $perPage);
}

include_once("footer.inc.php");
?>