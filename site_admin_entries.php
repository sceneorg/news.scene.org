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

if (@$_POST["id"])
{
  SQLLib::UpdateRow("entries",array(
    "status"=>$_POST["status"],
    "title"=>$_POST["title"],
    "contents"=>$_POST["contents"],
  ),sprintf_esc("id=%d",$_POST["id"]));
  flushCaches();
  header("Location: ".ROOT_URL."admin/entries/?id=".(int)$_POST["id"]);
  exit();
}

$TITLE = "admin";
include_once("header.inc.php");

if (@$_GET["id"])
{
  $item = SQLLib::SelectRow(sprintf_esc("SELECT * FROM entries WHERE id = %d",$_GET["id"]));
?>
<link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>jodit.min.css"/>
<script src="<?=ROOT_URL?>jodit.min.js"></script>

<form method="post">
  <input type="hidden" name="id" value="<?=_html($item->id)?>"/>
  <label>
    News item title:
    <input type="text" name="title" required="yes" value="<?=_html($item->title)?>"/>
  </label>
  
  <label>
    News item contents:
    <textarea name="contents" id="newsContents" required="yes"><?=_html(@$item->contents)?></textarea>
  </label>
  
  Status:
  <label><input type="radio" name="status" value="pending"  <?=($item->status=="pending" ?"checked='checked'":"")?>/> Pending</label>
  <label><input type="radio" name="status" value="approved" <?=($item->status=="approved"?"checked='checked'":"")?>/> Approved</label>
  <label><input type="radio" name="status" value="rejected" <?=($item->status=="rejected"?"checked='checked'":"")?>/> Rejected</label>
  
  <input type="submit" value="Save"/>
</form>

<script>
Jodit.make('#newsContents',{
  "buttons": "cut,copy,undo,redo,|,bold,italic,underline,ul,ol,link,|,source",
  "allowResizeY": true,
  "height": 400,
  "commandToHotkeys": {},
  "link": { "processVideoLink": false, },
});
</script>

<?php
}
else
{
  $items = SQLLib::SelectRows("SELECT * FROM entries ORDER BY id DESC");
  echo "<ul>\n";
  foreach($items as $item)
  {
    printf("  <li><a href='".ROOT_URL."admin/entries/?id=%d'>%s</a></li>\n",$item->id,_html($item->title));
  }
  echo "</ul>\n";
}

include_once("footer.inc.php");
?>