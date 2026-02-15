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
  $sideload = new Sideload();
  $result = $sideload->Request($_POST["addUrl"]);

  if ($result)
  {
    $title = "";
    $items = parseFeedToItems($result, $feedTitle);
    if ($items)
    {
      $date = date("Y-m-d H:i:s");
      $feedID = SQLLib::InsertRow("feeds",array(
        "url"=>$_POST["addUrl"],
        "title"=>$feedTitle,
        "lastChecked"=>$date,
        "lastHTTPResult"=>$sideload->httpReturnCode
      ));
      foreach($items as $item)
      {
        SQLLib::InsertRow("entries",array(
          "retrievalDate" => $item["postDate"],
          "postDate" => $item["postDate"],
          "title" => $item["title"],
          "contents" => $item["contents"],
          "sourceFeedID" => $feedID,
          "sourceFeedGUID" => $item["guid"],
          "status" => "approved",
        ));
      }
      header("Location: ".ROOT_URL."admin/feeds/?success");
      exit();
    }
  }
  header("Location: ".ROOT_URL."admin/feeds/?error");
  exit();
}

if(@$_POST["deleteID"])
{
  SQLLib::Query(sprintf_esc("DELETE FROM entries WHERE sourceFeedID=%d",$_POST["deleteID"])); // ?
  SQLLib::Query(sprintf_esc("DELETE FROM feeds WHERE id=%d",$_POST["deleteID"]));
  header("Location: ".ROOT_URL."admin/feeds");
  exit();
}

$TITLE = "admin";
include_once("header.inc.php");

$items = SQLLib::SelectRows("SELECT * FROM feeds");
echo "<table>\n";
foreach($items as $item)
{
  printf("<tr>\n");
  printf("  <td>%s</td>\n",_html($item->title));
  printf("  <td><a href='%s'>%s</a></td>\n",$item->id,_html($item->url),_html($item->url));
  printf("  <td><span class='statuscode-%s'>%d</span></td>\n",$item->lastHTTPResult>=200&&$item->lastHTTPResult<=399?"success":"error",$item->lastHTTPResult);
  printf("  <td><form method='post'><input type='hidden' name='deleteID' value='%d'/><input type='submit' value='Delete'/></form></td>\n",$item->id,_html($item->url),_html($item->url));
  printf("</tr>\n");
}
echo "</table>\n";

echo "<form method='post'><input type='url' name='addUrl'/><input type='submit' value='Add'/></form>";

include_once("footer.inc.php");
?>