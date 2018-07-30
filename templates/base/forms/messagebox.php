


<?php
if(!defined("msgButtonBack"))			define("msgButtonBack",1);
if(!defined("msgButtonOk"))				define("msgButtonOk",2);
if(!defined("msgButtonCancel"))			define("msgButtonCancel",3);



function MessageBox($string,$buttons=Array(),$out=true)
{
	$buttonsList = Array(msgButtonBack=>Array('icon'=>'button-icon-ok','msg'=>'Назад'),msgButtonOk=>Array('icon'=>'button-icon-ok','msg'=>'Ок.'),msgButtonCancel=>Array('icon'=>'button-icon-cancel','msg'=>'Отмена'));

	$res =  "<div class='message-box message-notice'>";
	$res .= "<p>".$string."</p>";
	
	
	
	
	
	$res.="<div class='message-box-footer'>";
	foreach($buttons as $key => $val){
		
		$res.="<button type='button' class='normal-button message-button' onclick='$val'>";
		$res.="<div class='button-icon ".$buttonsList[$key]['icon']."'></div>";
		$res.=$buttonsList[$key]['msg'];
		$res.="</button>";
	}
	$res.="</div>";
	$res .= "</div>";	
	
	if($out) echo $res;
	else return $res;
}

?>