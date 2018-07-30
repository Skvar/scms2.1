<?php


echo "<br>";
$out['postheader'] = "Документы ".$GLOBALS['organization']['name']."<hr>";
$out['post'] = "Документы связанные с деятельностью ".$GLOBALS['organization']['name'].".";		

tInsertPost($out,'docblock',false);	
$Files = Array('filelist'=>'list','show'=>true,'type'=>7,'owner'=>$tmp['id'],'header'=>'Отчеты:');	
include("files.php");
					
$Files = Array('filelist'=>'list','show'=>true,'type'=>4,'owner'=>$tmp['id'],'header'=>'Прочие документы:');	
include("files.php");
?>