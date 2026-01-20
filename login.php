<?php
require_once("bootstrap.inc.php");
require_once("functions.inc.php");

if (!$_GET["code"])
{
  $_SESSION["__return"] = $_GET["return"];
  $sceneID->PerformAuthRedirect();
  exit();
}

$sceneID->ProcessAuthResponse();

unset($_SESSION["userID"]);

session_regenerate_id(true);

$SceneIDuser = $sceneID->Me();

if (!$SceneIDuser["success"] || !$SceneIDuser["user"]["id"])
{
	exit("not auth");
}

$pouetUserData = @file_get_contents("http://api.pouet.net/v1/user/?id=".(int)$SceneIDuser["user"]["id"]);
if($pouetUserData)
{
  $pouetUser = json_decode($pouetUserData,true);
  if($pouetUser["user"]["level"] == "banned")
  {
    die("no");
  } 
}

$sceneID = (int)$SceneIDuser["user"]["id"];

$_SESSION["userID"] = $sceneID;

header("Location: ".($_SESSION["__return"] ? $_SESSION["__return"] : ROOT_URL."submit/"));
?>