<?php 
$sst = session_status();
if($sst == PHP_SESSION_NONE) session_start();
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '200M');
ini_set('session.gc_maxlifetime', 60*3000);
ini_set('session.gc_divisor', 1000);
//---------------------------------------------------------
require_once("errors.php");
//---------------------------------------------------------
require_once('conf.php');

//---------------------------------------------------------
require_once("defines.php");
require_once("core.php");
require_once("db.php");
require_once("template.php");
//---------------------------------------------------------
function sysGetMikrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
//-------------------------------------------------------
$GLOBALS['loadtime'] = 0;
$GLOBALS['loadtimestart'] = sysGetMikrotime();

$mysdb = new CMYSQL("dbconf.php");
$GLOBALS['dbptr'] = $mysdb;

//---------------------------------------------------------
$res = coreInit();
/**********************************************************************************************************/
?>
<!DOCTYPE html>
<html lang="RU-ru">
<?php
include("header.php");
include("checkbro.php");
?>
<body>
<?php
tPutHeader();

coreSafeFunction('Content');

tPutFooter();
?>
</body>
</html>


