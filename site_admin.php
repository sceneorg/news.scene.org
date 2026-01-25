<?php
include_once("bootstrap.inc.php");

if (!$isAdmin)
{
  header("Location: ".ROOT_URL);
  exit();
}

if (@$_POST["id"] && @$_POST["decision"])
{
  SQLLib::UpdateRow("entries",array("status"=>$_POST["decision"]),sprintf_esc("id=%d",$_POST["id"]));
  flushCaches();
  header("Location: ".ROOT_URL."admin");
  exit();
}

include_once("header.inc.php");

$items = SQLLib::SelectRows("SELECT * FROM entries WHERE status='pending' ORDER BY id DESC");
foreach($items as $item)
{
  echo "<form method='post'>\n";
  printf("<input type='hidden' name='id' value='%d'>\n",$item->id);
  printf("<input type='submit' name='decision' value='approved'>\n");
  printf("<input type='submit' name='decision' value='rejected'>\n");
  echo "<article>\n";
  printf("  <h2><a href='%s'>%s</a></h2>\n",_html(getNewsUrl($item)),_html($item->title));
  printf("  <div>%s</div>\n",processPost($item->contents));
  echo "</article>\n";
  echo "</form>\n\n";
}

include_once("footer.inc.php");
?>