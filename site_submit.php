<?php
include_once("bootstrap.inc.php");

if (!@$_SESSION["userID"])
{
  header("Location: ".ROOT_URL."login");
  exit();
}

if (@$_POST["newsTitle"] && @$_POST["newsContents"])
{
  $SceneIDuser = $sceneID->Me();

  SQLLib::InsertRow("entries",array(
    "retrievalDate" => date("Y-m-d H:i:s"),
    "postDate" => date("Y-m-d H:i:s"),
    "title" => $_POST["newsTitle"],
    "contents" => processPost($_POST["newsContents"]."\n<br/>[<b>Submitted by "._html($SceneIDuser["user"]["display_name"])."</b>]"),
    "submittedUserID" => (int)$_SESSION["userID"],
  ));
  header("Location: ".ROOT_URL."submit?success");
  exit();
}

$IITLE = "submit a news item!";
include_once("header.inc.php");

if (isset($_GET["success"]))
{
  echo "<div class='success'>Thank you for your submission! It is now in a queue and will be processed eventually!</div>";
}
else
{
?>

<link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>jodit.min.css"/>
<script src="<?=ROOT_URL?>jodit.min.js"></script>

<form method="post">
  <label>
    News item title:
    <input type="text" name="newsTitle" required="yes" value="<?=_html(@$_POST["newsTitle"])?>"/>
  </label>
  
  <label>
    News item contents:
    <textarea name="newsContents" id="newsContents" required="yes"><?=_html(@$_POST["newsContents"])?></textarea>
  </label>
  
  <input type="submit" value="Send!"/>
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

include_once("footer.inc.php");
?>