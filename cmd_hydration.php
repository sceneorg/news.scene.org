<?php
if (php_sapi_name() != "cli")
  die("commandline only!");

exit();

include_once(dirname(__FILE__) . "/bootstrap.inc.php");
include_once(dirname(__FILE__) . "/sideload.inc.php");

$date = date("Y-m-d H:i:s");

SQLLib::Query("TRUNCATE entries;");

$frequency = 60 * 15;
$feeds = SQLLib::SelectRows("SELECT * FROM feeds");

$items = array();
foreach($feeds as $feed)
{
  printf("%s\n",$feed->url);
  $sideload = new Sideload();
  $result = $sideload->Request($feed->url);

  if ($result)
  {
    $rss = new SimpleXMLElement($result);
    if ($rss && $rss->channel)
    {
      $feedTitle = $rss->channel->title;
      foreach($rss->channel->item as $item)
      {
        $items[] = array(
          "retrievalDate" => $date,
          "postDate" => $item->pubDate ? date("Y-m-d H:i:s",strtotime($item->pubDate)) : $date,
          "title" => $item->title,
          "contents" => sprintf("<p>[ <b>%s</b> ] <a href=\"%s\">%s</a></p>\n%s\n",_html($feedTitle),_html($item->link),_html($item->title),processPost($item->description)),
          "sourceFeedID" => $feed->id,
          "sourceFeedGUID" => $item->guid,
          "status" => "approved",
        );
      }
    }
  }
  SQLLib::UpdateRow("feeds",array("lastChecked"=>$date,"lastHTTPResult"=>$sideload->httpReturnCode),sprintf_esc("id=%d",$feed->id));
}

usort($items,function($a,$b){
  return strcmp($a["postDate"],$b["postDate"]);
});

SQLLib::InsertMultiRow("entries",$items);