<?php

$query = "SELECT DATE_FORMAT(time, '%d.%m.%Y') as lastlogin, user, COUNT(ip) as visits FROM `livComStats` WHERE user <>0 AND ip <> '95.188.83.133' GROUP BY lastlogin,user ORDER BY lastlogin DESC";
$tab = $dbptr->LoadData($query);


//$tab[]=Array('lastlogin'=>'14.02.2018','visits'=>0);
$stats = Array();
$Histogramm = Array('width'=>900,'height'=>200,'type'=>'1','period'=>31,'direction'=>true);
foreach($tab as $ix => $data){
	$d = explode('.',$data['lastlogin']);
	$pos = intval($d[0]);
	
	if(!isset($Histogramm['data'][$d[1].".".$d[2]])) $Histogramm['data'][$d[1].".".$d[2]] = array_fill(1,31,0);
	$Histogramm['data'][$d[1].".".$d[2]][$pos] ++;// $data['visits'];
	
	if(isset($stat[$d[1].$d[2]]))   $stat[$d[1].$d[2]] ++;//= $data['visits'];	
	else 							$stat[$d[1].$d[2]] = 0;//$data['visits'];	
}
$date = getdate();
$allvisit = 0;
$curmounthvisit = 0;
$averagevisit = 0;
foreach($stat as $per => $count)
{
	if($per == $date['mon'].$date['year']) $curmounthvisit+=$count;
	
	$allvisit+=$count;
}

if(count($stat)>0) $averagevisit = $allvisit/count($stat);
	
	$out['postheader'] = "Статистика посещаемости";
	$out['post'] = "Всего с начала учета: ".$allvisit."<br>За текущий месяц: ".$curmounthvisit."<br>В среднем в месяц: ".$averagevisit;
	$out['post'] .="<hr>";	
	$out['post'] .= corePutForm("histogramm.php",$Histogramm);
	
	//$out['post'] .= "<pre>".(print_r($Histogramm,true))."</pre>";
	
	
	tInsertPost($out,'user-message',false);



?>