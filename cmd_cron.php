<?php
if (php_sapi_name() != "cli")
  die("commandline only!");

include_once(dirname(__FILE__) . "/bootstrap.inc.php");
include_once(dirname(__FILE__) . "/sideload.inc.php");

$date = date("Y-m-d H:i:s");

$frequency = 60 * 15;
$feed = SQLLib::SelectRow(sprintf_esc("SELECT * FROM feeds WHERE (lastChecked IS NULL OR TIMESTAMPDIFF(SECOND,lastChecked,'%s') > %d) ORDER BY id",$date,$frequency));
if (!$feed) die();

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
      $row = SQLLib::SelectRow(sprintf_esc("SELECT 1 FROM entries WHERE sourceFeedID=%d AND sourceFeedGUID='%s'",$feed->id,$item->guid));
      if (!$row)
      {
        SQLLib::InsertRow("entries",array(
          "retrievalDate" => $date,
          "postDate" => $item->pubDate ? date("Y-m-d H:i:s",strtotime($item->pubDate)) : $date,
          "title" => $item->title,
          "contents" => sprintf("<p>[ <b>%s</b> ] <a href=\"%s\">%s</a></p>\n%s\n",_html($feedTitle),_html($item->link),_html($item->title),processPost($item->description)),
          "sourceFeedID" => $feed->id,
          "sourceFeedGUID" => $item->guid,
        ));
      }
    }
  }
}

SQLLib::UpdateRow("feeds",array("lastChecked"=>$date,"lastHTTPResult"=>$sideload->httpReturnCode),sprintf_esc("id=%d",$feed->id));
