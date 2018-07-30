<?php
if(isset($_REQUEST['ajax'])) unset($_REQUEST['ajax']);
$sst = session_status();
if($sst == PHP_SESSION_NONE) session_start();
require_once("errors.php");
require_once('conf.php');
//---------------------------------------------------------
require_once("defines.php");
require_once("core.php");
require_once("db.php");
require_once("template.php");

$mysdb = new CMYSQL("dbconf.php");
$GLOBALS['dbptr'] = $mysdb;


$user = isset($_SESSION['user']) ? $_SESSION['user'] : Array();
?>