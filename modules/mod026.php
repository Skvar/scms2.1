<?php
echo "<br>";
$Form = Array('handler'=>'modules/mod026.php','arg'=>'');

$Form['streets'] 	= $dbptr->LoadData("SELECT id,street FROM livBuilding WHERE type = 1");
$Form['statuses'] 		= coreLoadProperty("workStatus");
$Form['filter-date-beg'] = date("Y")."-01";
$Form['filter-date-end'] = date("Y")."-12";



echo corePutForm("FilterMaintenance.php",$Form);
?>