<?php
include_once("credentials.inc.php");
include_once("unhandled-exception.inc.php");
include_once("sqllib.inc.php");
include_once("functions.inc.php");
include_once("sideload.inc.php");
include_once("sceneid3lib-php/sceneid3.inc.php");

$lifetime = 60 * 60 * 24 * 15;
@ini_set('session.cookie_lifetime', $lifetime);

$url = parse_url(ROOT_URL);
session_name("NEWS");
session_set_cookie_params($lifetime, $url["path"], @$url["domain"]);
@session_start();

$sceneID = new SceneID3( array(
  "clientID" => SCENEID_USER,
  "clientSecret" => SCENEID_PASS,
  "redirectURI" => ROOT_URL . "login/",
) );
  
$metaValues = array();
$TITLE = "";

$isAdmin = @$_SESSION["userID"] ? !!SQLLib::SelectRow(sprintf_esc("SELECT 1 FROM admins WHERE sceneID = %d",$_SESSION["userID"])) : false;

