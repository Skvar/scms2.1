<?php

//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : doNope;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$wid = isset($_REQUEST['workid']) ? $_REQUEST['workid'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	
	coreHideInclude("mod2.php");

	$user = GetCurrentUser();
	$date = date("Y-m-d");
	$build = buildGetList($id);	
	$dbptr = $GLOBALS['dbptr'];
	$result = Array('result'=>false,'message'=>'','useform'=>false);
	
	switch($command){
		case doEdit:
			$work = $dbptr->LoadRow("SELECT * FROM livBuildMaintenance WHERE id=$wid");
			$work['bill'] = floatval(str_replace(",",".",$work['bill']));		
		case doAdd:
			$Form['statuses'] 		= coreLoadProperty("workStatus");
			$Form['contractors'] 	= $dbptr->LoadData("SELECT id,name FROM livOrganizations WHERE type IN (1,2,3,5)");
			$Form['postdate'] 		= isset($work['workdate'])		? $work['workdate'] 	: $date;
			$Form['work'] 			= isset($work['description'])	? $work['description'] 	: "";
			$Form['workvolume'] 	= isset($work['volume']) 		? $work['volume'] 		: "";
			$Form['bill'] 			= isset($work['bill']) 			? $work['bill'] 		: "";
			$Form['contractor']		= isset($work['contractor']) 	? $work['contractor'] 	: 0;
			$Form['status']			= isset($work['status']) 		? $work['status']		: 0;
				
			$Form['handler'] = "modules/mod213.php";
			$Form['arg'] = "&command=".doSave."&workid=".$wid."&id=".$id;
		
			$result['message'] = corePutForm("BuildMaintenanceForm.php",$Form);
			$result['header'] = "Текущий ремонт дома ".$build['name'];
			$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
			$result['user'] = $user['name'];
			$result['useform'] = true;	
			$result['result'] = true;
		break;
		case doSave:
			$vals = json_decode($data,true);
			$volume = $vals['work-volume'];
			$bill	= $vals['work-bill'];
			$date 	= $vals['work-date'];
			$contr  = $vals['work-contractor'];
			$status = $vals['work-status'];
			$descr  = $vals['work-description'];
			$cngdate= date("Y-m-d");
			$userid = $user['id'];
				
			if($wid)	$query = "UPDATE livBuildMaintenance SET description='$descr', bill='$bill',volume='$volume',status=$status,workdate='$date',contractor=$contr,changedate='$cngdate' WHERE id=$wid";
			else		$query = "INSERT livBuildMaintenance (description,bill,workdate,volume,status,contractor,build,user,changedate) VALUES('$descr','$bill','$date','$volume',$status,$contr,$id,$userid,'$cngdate')";
			
			if($dbptr->Query($query)){
				$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
				$result['reload'] = true;
				$result['result'] = true;
						
			}
			else{
				$result['reload'] = false;
				$result['message'] = "Ошибка сохранения<br>".$query;
			}		
		break;
		case doDelete:
			$query = "DELETE FROM livBuildMaintenance WHERE id=$wid";
			if($dbptr->Query($query)){
				$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
				$result['reload'] = true;
				$result['result'] = true;		
			}
			else{
				$result['reload'] = false;
				$result['message'] = "Ошибка удаления<br>".$query;
			}						
		break;
	}
	
	unset($_REQUEST['ajax'],$_REQUEST['$command'],$_REQUEST['data'],$_REQUEST['wid'],$_REQUEST['id']);
	
	echo json_encode($result);	
}
else{

	echo "<br>";
	$out['postheader'] = "Информация о текущем ремонте дома";
	
	
	$stats = coreLoadProperty("workStatus");
	
	$user = GetCurrentUser();

	$query = "SELECT DATE_FORMAT(bm.workdate, '%d.%m.%Y') as workdate,DATE_FORMAT(bm.changedate, '%d.%m.%Y') as changedate,DATE_FORMAT(bm.workdate, '%Y') as workyear,DATE_FORMAT(bm.workdate, '%m') as workmonth,bm.id,bm.description,bm.bill,bm.volume,bm.status,bm.contractor,bm.user,org.name
				FROM livBuildMaintenance bm
				LEFT JOIN livOrganizations org on org.id = bm.contractor  
				WHERE bm.build = ".$build['id']." ORDER BY bm.workdate DESC";
	$tmp = $dbptr->LoadData($query);
	
	$work = Array();
	$summbill = 0;
	$numworks = 0;
	foreach($tmp as $works => $w){
		$cbill = floatval(str_replace(",",".",$w['bill']));
		$work[$w['workyear']][$w['workmonth']][$w['status']][] = $w;
		if(isset($work[$w['workyear']]['bill'])) $work[$w['workyear']]['bill']+=$cbill;
		else									 $work[$w['workyear']]['bill']=$cbill;

		$summbill +=$cbill; 
		$numworks++;	
		
		unset($w);
	}
	unset($tmp);
	
	$tmp = getdate();
	$cyear = $tmp['year'];
	unset($tmp);
	
	
	$out['post'] = "Всего проведено $numworks ".morphA($numworks,"а","ы","работ").", на сумму ".number_format($summbill, 2, ',', ' ')." руб.";
	tInsertPost($out,'workblock',false);
	
	foreach($work as $year => $wmonth){
		
		$out['header'] = "Текущий ремонт за ".$year." год, общей стоимостью: ".number_format( str_replace(",",".",$work[$year]['bill']), 2, ',', ' ')." руб. ";
		$out['icon'] = './templates/base/images/icon_buildmnt.png';	
		
		$ix = 0;
		foreach($wmonth as $month => $wdays){
			if(intval($month)>0){
				$pworks = isset($wdays[0]) ? count($wdays[0]) : 0;
				$dworks = isset($wdays[1]) ? count($wdays[1]) : 0;
				$fworks = isset($wdays[2]) ? count($wdays[2]) : 0;
				$out['out'][$ix]['header'] = "На ".strftime("%B %Y",strtotime($year."-".$month))." Запланированно: ".($pworks+$dworks+$fworks)." ".morphA(($pworks+$dworks+$fworks),"а","ы","работ").", выполнено работ: ".$dworks;
				$text ="<table border='1' width='100%'>"; 
				$npp = 1;
				
		
					$text .= "<tr>";
					$text .= "<th width='5%'>№ п/п</th>";
					$text .= "<th width='40%'>Описание работ</th>";
					$text .= "<th width='10%'>Объем работ</th>";
					$text .= "<th width='10%'>Стоимость работ</th>";
					$text .= "<th width='10%'>Статус выполнения</th>";	
					$text .= "<th>Подрядчик</td>";
					$text .= "</tr>";
				
					
				for($stat = 0;$stat<3;$stat++){
					if(isset($wdays[$stat])){
						for($a=0;$a<count($wdays[$stat]);$a++){
							$bill = round(   str_replace(",",".", $wdays[$stat][$a]['bill'] ),2 );
							$text .= "<tr>";
							$text .= "<td height='22px'>".$npp.".</td>";
							$text .= "<td>".$wdays[$stat][$a]['description']."</td>";
							$text .= "<td>".$wdays[$stat][$a]['volume']."</td>";
							$text .= "<td>".number_format($bill, 2, ',', ' ')."</td>";
							$text .= "<td>".$stats[$stat]."</td>";	
							$text .= "<td>";
							
							if($user['right']>=100){
								$com = $GLOBALS['command'];
								$mod = $GLOBALS['module'];
								$wid = $wdays[$stat][$a]['id'];

			 					$text .= tInsertSmallButtons("modules/mod213.php","workid=$wid&id=".$build['id'],['edit'=>doEdit,'delete'=>doDelete],20,-2,true);	
							}
			
							$text .= $wdays[$stat][$a]['name']."</td></tr>";	
							
							$npp++;	
					
						}
					}
				}		
				$text .="</table>";
				$out['out'][$ix]['text'] = $text; 
				$ix++;
	
			}
		}	
		if($year == $cyear)		tInsertHierarchyBlock($out,'services',true);
		else					tInsertHierarchyBlock($out,'services');
		

		
		unset($out);
	}	
}

?>