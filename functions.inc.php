<?php
function _html( $s )
{
  return htmlspecialchars( $s ?: "", ENT_QUOTES );
}
function _js( $s )
{
  return addcslashes( $s ?: "", "\x00..\x1f'\"\\/" );
}
function _like( $s )
{
  return addcslashes( $s ?: "", "%_" );
}

function shortify( $text, $length = 100 )
{
  if (mb_strlen($text,"utf-8") <= $length) return $text;
  $z = mb_stripos($text," ",$length,"utf-8");
  return mb_substr($text,0,$z?$z:$length,"utf-8")."...";
}

function split_search_terms( $str )
{
  preg_match_all('/([^\s"]+)|"([^"]*)"/',$str,$m);
  $terms = array();
  foreach($m[0] as $k=>$v)
    $terms[] = $m[1][$k] ? $m[1][$k] : $m[2][$k];
  return $terms;
}

function hashify($s)
{
  $hash = strtolower($s);
  $hash = preg_replace("/[^\w]+/","-",$hash);
  $hash = preg_replace("/^[_]+/","",$hash);
  $hash = preg_replace("/[_]+$/","",$hash);
  $hash = trim($hash,"-");
  return $hash;
}

function dateDiffReadable( $a, $b )
{
  if (is_string($a)) $a = strtotime($a);
  if (is_string($b)) $b = strtotime($b);
  $dif = $a - $b;
  if ($dif < 60) return "a few moments ago"; $dif /= 60;
  if ($dif < 60) return (int)$dif." minutes ago"; $dif /= 60;
  if ($dif < 24) return (int)$dif." hours ago"; $dif /= 24;
  if ($dif < 30) return (int)$dif." days ago"; $dif /= 30;
  if ($dif < 12) return (int)$dif." months ago"; $dif /= 12;
  return (int)$dif." years ago";
}

function paginator($totalCount,$perPage)
{
  $key = "p";
  $page = @(int)$_GET[$key];
  if ($totalCount > $perPage)
  {
    $pageCount = ceil( $totalCount / $perPage );
    echo "<div id='paginator'>\n";
    echo "<ul>\n";
    if ($page > 1)
    {
      $get = $_GET;
      $get[$key] = $page - 1;
      printf("<li><a href='?%s'>&laquo; Prev</a></li>\n",http_build_query($get));
    }
    for($x = 0; $x < $pageCount; $x++)
    {
      $get = $_GET;
      $get[$key] = $x + 1;
      
      $d = ($x + 1) - ($page ? $page : 1);
      if ($d == 0)
      {
        printf("<li>%d</li>\n",$x + 1);
        $lastWasDigit = true;
      }
      else
      {
        if (($x < 3) || ($pageCount - $x <= 3) || (abs($d) <= 2))
        {
          $lastWasDigit = true;
          printf("<li><a href='?%s'>%d</a></li>\n",http_build_query($get),$x + 1);
        }
        else
        {
          if ($lastWasDigit)
          {
            printf("<li>&hellip;</li>\n");
            $lastWasDigit = false;
          }
        }
      }
    }
    if ($page < $pageCount)
    {
      $get = $_GET;
      $get[$key] = ($page ? $page : 1) + 1;
      printf("<li><a href='?%s'>Next &raquo;</a></li>\n",http_build_query($get));
    }  
    echo "</ul>\n";
    echo "</div>\n";
  }
}

function processPost($text)
{
  $text = strip_tags($text,"<a><b><i><strong><em><p><ol><ul><li>");
  return $text;
}

function retrieveFeed(&$items, &$total, $page, $perPage)
{
  $items = SQLLib::SelectRows(sprintf_esc("SELECT SQL_CALC_FOUND_ROWS id, title, contents, retrievalDate FROM entries WHERE status='approved' ORDER BY retrievalDate DESC, postDate DESC LIMIT %d OFFSET %d",$perPage, $page * $perPage));
  $total = SQLLib::SelectRow("SELECT FOUND_ROWS() AS total")->total;
}

function getNewsUrl($obj)
{
  return ROOT_URL."news/".(int)$obj->id."/".hashify($obj->title);
}

function formatFeedToRSS($items)
{
  $output = '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>'."\n";
  $date = date("r");
  $year = date("Y");
  $root = ROOT_URL;
  $output .= <<<END
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>Scene.org News</title>
    <link>{$root}</link>
    <description>Demoscene news service operated by Scene.org</description>
    <language>en-us</language>
    <docs>http://feedvalidator.org/docs/rss2.html</docs>

    <atom:link href="{$root}/feeds/rss/" rel="self" type="application/rss+xml" />

    <lastBuildDate>{$date}</lastBuildDate>
    <copyright>Copyright {$year} scene.org</copyright>
    <ttl>60</ttl>
END;
  foreach ($items as $item)
  {
    $id = (int)$item->id;
    $title = _html($item->title);
    $html = $item->contents;
    $date = date("r",strtotime($item->retrievalDate));
    $url = getNewsUrl($item);
    
    $output .= <<<END
      <item>
        <guid isPermaLink="true">{$url}</guid>
        <link>{$url}</link>
        <title>{$title}</title>
        <description><![CDATA[{$html}]]></description>
        <pubDate>{$date}</pubDate>
      </item>

  END;
  }
  $output .= <<<END
  </channel>
</rss>
END;
  return $output;
}

function formatFeedToAtom($items)
{
  $output = '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>'."\n";
  $date = date("c");
  $year = date("Y");
  $root = ROOT_URL;
  $output .= <<<END
<feed xmlns="http://www.w3.org/2005/Atom">

  <title>Scene.org News</title>
  <link href="{$root}feeds/atom/" rel="self" />
  <link href="{$root}" />
  <id>{$root}</id>
  <updated>{$date}</updated>
  
END;
  foreach ($items as $item)
  {
    $id = (int)$item->id;
    $title = _html($item->title);
    $html = $item->contents;
    $date = date("c",strtotime($item->retrievalDate));
    $url = getNewsUrl($item);
    
    $output .= <<<END
  <entry>
    <title>{$title}</title>
    <link href="{$url}" />
    <id>{$url}</id>
    <published>{$date}</published>
    <updated>{$date}</updated>
    <content type="xhtml">
      <div xmlns="http://www.w3.org/1999/xhtml">
        {$html}
      </div>
    </content>
    <author>
      <name>Scene.org</name>
    </author>
  </entry>
  END;
  }
  $output .= <<<END
</feed>
END;
  return $output;
}

function getFeedCacheRSS()
{
  @mkdir("cache",0775);
  $filename = "cache/feed_cache.rss";
  if (!file_exists($filename))
  {
    $page = 0;
    $perPage = 10;
    $items = array();
    $total = 0;
    retrieveFeed($items, $total, $page, $perPage);
    
    file_put_contents($filename,formatFeedToRSS($items));
  }
  
  return file_get_contents($filename);
}

function getFeedCacheAtom()
{
  @mkdir("cache",0775);
  $filename = "cache/feed_cache.atom";
  if (!file_exists($filename))
  {
    $page = 0;
    $perPage = 10;
    $items = array();
    $total = 0;
    retrieveFeed($items, $total, $page, $perPage);
    
    file_put_contents($filename,formatFeedToAtom($items));
  }
  
  return file_get_contents($filename);
}

function getFeedCacheJSON()
{
  @mkdir("cache",0775);
  $filename = "cache/feed_cache.json";
  if (!file_exists($filename))
  {
    $page = 0;
    $perPage = 10;
    $items = array();
    $total = 0;
    retrieveFeed($items, $total, $page, $perPage);
    
    $result = array();
    $result["lastBuildDate"] = date("r");
    $result["items"] = array_map(function($i){ 
      $i->id = (int)$i->id;
      $i->pubDate = date("r",strtotime($i->retrievalDate));
      unset($i->retrievalDate);
      $i->url = getNewsUrl($i);
      return $i; 
    }, $items);
    file_put_contents($filename,json_encode($result,JSON_PRETTY_PRINT));
  }
  
  return file_get_contents($filename);
}

function flushCaches()
{
  @unlink("cache/feed_cache.rss");
  @unlink("cache/feed_cache.json");
  @unlink("cache/feed_cache.atom");
}

function parseFeedToItems($feedString, &$outFeedTitle)
{
  $xml = @new SimpleXMLElement($feedString);
  if (!$xml)
  {
    return false;
  }
  
  $date = date("Y-m-d H:i:s");
  $items = array();
  if ($xml->channel)
  {
    // RSS
    $outFeedTitle = $xml->channel->title;
    foreach($xml->channel->item as $item)
    {
      $items[] = array(
        "postDate" => $item->pubDate ? date("Y-m-d H:i:s",strtotime($item->pubDate)) : $date,
        "title" => $item->title,
        "contents" => sprintf("<p>[ <b>%s</b> ] <a href=\"%s\">%s</a></p>\n%s\n",_html($outFeedTitle),_html($item->link),_html($item->title),processPost($item->description)),
        "guid" => $item->guid
      );
    }
  }
  else if ($xml->entry)
  {
    // ATOM
    $outFeedTitle = $xml->title;
    foreach($xml->entry as $item)
    {
      $items[] = array(
        "postDate" => $item->published ? date("Y-m-d H:i:s",strtotime($item->published)) : $date,
        "title" => $item->title,
        "contents" => sprintf("<p>[ <b>%s</b> ] <a href=\"%s\">%s</a></p>\n%s\n",_html($outFeedTitle),_html($item->link["href"]),_html($item->title),processPost($item->content)),
        "guid" => $item->id
      );
    }
  }
  return $items;
}