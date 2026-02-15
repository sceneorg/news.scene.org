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
  $feedTitle = "";
  $items = parseFeedToItems($result, $feedTitle);
  if ($items)
  {
    $guids = array_map(function($i){ return $i->sourceFeedGUID; }, SQLLib::SelectRows(sprintf_esc("SELECT sourceFeedGUID FROM entries WHERE sourceFeedID = %d",$feed->id)));
    foreach($items as $item)
    {
      if(in_array($item["guid"],$guids))
      {
        continue;
      }
      SQLLib::InsertRow("entries",array(
        "retrievalDate" => $date,
        "postDate" => $item["postDate"],
        "title" => $item["title"],
        "contents" => $item["contents"],
        "sourceFeedID" => $feed->id,
        "sourceFeedGUID" => $item["guid"],
      ));
    }
  }
}

SQLLib::UpdateRow("feeds",array(
  "title"=>$feedTitle,
  "lastChecked"=>$date,
  "lastHTTPResult"=>$sideload->httpReturnCode
),sprintf_esc("id=%d",$feed->id));
