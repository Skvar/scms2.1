<?php
if(!defined("doSaveCounters"))			define("doSaveCounters",1);
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : Array();
	
	$user = GetCurrentUser();
	$accountID = $user['account'];
	$date = date("Y-m-d");
	$dateof = date("Y-m-01");
	
	$result = Array('result'=>false,'message'=>'011');
	$msgstring = "";
	
	
	switch($mode){
		case doSaveCounters://Сохраняем показания счетчиков---------------------------------------

			$values = json_decode($data);
			$err = 0;
			foreach($values as $key => $val){
				$mr = isset($GLOBALS['settings']['maxResourceValue']) ? $GLOBALS['settings']['maxResourceValue'] : 30;
				
					//Проверка есть ли уже показания в этом месяце----------------------------
					$query = "SELECT id FROM livCountersValue WHERE MONTH(dateof) = MONTH(CURDATE()) AND YEAR(dateof) = YEAR(CURDATE()) AND counter=$key";
					$tmp = $mysdb->LoadRow($query);
					if($tmp===false){//если нет то вставляем новые----------------------------		
						$query = "INSERT livCountersValue (account,dateof,valdate,value,counter,status) VALUES($accountID,'$dateof','$date',$val,$key,1)";
						//$result['message'].=$query;				
					}
					else{//Если да то обновляем данные----------------------------------------
						$recid = $tmp['id'];
						$query = "UPDATE livCountersValue SET valdate='$date',value=$val,status=1 WHERE id=$recid";	
						//$result['message'].=$query;	
					}
					
					if(!$mysdb->Query($query)){
							$result['message'].="Ошибка сохранения данных<br>.$query";
							$err++;
					}
			}
			
			if(!$err){
				include("forms/messagebox.php");
				 $result['result'] = true;
				 $msgstring = "Показания успешно сохранены.";
				 $msgbuttons = Array(msgButtonBack=>'location.reload()');	 
				 $result['message'] = MessageBox($msgstring,$msgbuttons,false);
			}
			else{
				include("forms/messagebox.php");
				$result['result'] = true;
				$msgbuttons = Array(msgButtonBack=>'location.reload()');
				$result['message'] = MessageBox($msgstring,$msgbuttons,false);
			}
		break;
	}
	

	unset($_REQUEST['ajax'],$_REQUEST['data'],$_REQUEST['mode']);
	
	echo json_encode($result);
	
}
else{
	
	
	coreHideInclude("modules/mod22.php");
	$srvIndexs = Array(500,600,700);
	$srv = Array();
	
	if(!$GLOBALS['countstats']['enabled']){
		include("forms/messagebox.php");	
		MessageBox("Прием показаний приостановлен.<br>Приём показаний обычно производится с 5 по 25 число каждого месяца.<br>По всем вопросам звоните: 8(39168) 4-30-26, 4-48-37");	
	}

	$Histogramm = Array('width'=>900,'height'=>200);
		
	foreach($srvIndexs as $key => $val){
		$tmp = loadServices($val,NULL);
		$srv[$val]['name'] = $tmp['name'];
		$srv[$val]['price'] = $tmp['prices']['price'];
		unset($tmp);
	}

	$Counters = $dbptr->LoadData("SELECT * FROM livCounters WHERE paccount=$accountID ORDER BY service");
	$Counters[] = Array('id' => 0,'service' => 700);

	
	foreach($Counters as $key => $val){
		$Counters[$key]['name'] = $srv[$val['service']]['name'];
		$Counters[$key]['price'] = $srv[$val['service']]['price'];	
		$cdate = getdate();
		if($val['id']){
			$query = "	SELECT DATE_FORMAT(dateof, '%d.%m.%Y') as dateof,DATE_FORMAT(valdate, '%d.%m.%Y') as date,value,counter,status,0 as rate 
						FROM livCountersValue 
						WHERE counter=".$val['id']." AND value<>0 AND valdate > '".($cdate['year']-3).($cdate['mon'])."01'  ORDER BY valdate DESC";
			$tmp = $dbptr->LoadData($query);
			
			if(count($tmp)){		
				$begmonth = strtotime($cdate['year']."-".$cdate['mon']."-01");

				$vdate = strtotime($tmp[0]['date']);
				if($vdate>=$begmonth){
					$Counters[$key]['values'][0] = $tmp[1];
					$Counters[$key]['values'][1] = $tmp[0];
				}
				else $Counters[$key]['values'][0] = $tmp[0];
						
				for($a=0;$a<count($tmp)-1;$a++){
					$tmp[$a]['rate'] = ($tmp[$a]['value'] - $tmp[$a+1]['value']);	
				}
				
					
				foreach($tmp as $ix => $d){								
					$dm = strftime("%B %Y",strtotime($d['date']));		
					if(isset($Histogramm['data'][$val['service']][$dm])) $Histogramm['data'][$srv[$val['service']]['name']][$dm] +=	$d['rate'];
					else												 $Histogramm['data'][$srv[$val['service']]['name']][$dm] =	$d['rate'];			
				}			
			}
			else{
				$Counters[$key]['values'][0] = Array('date'=>'00.00.0000','value'=>0,'counter'=>0,'status'=>0);
			}		
		}
	}
	
	$Form['counters'] = $Counters;
	
	

	$Form['handler'] = "modules/mod011.php";
	$Form['arg'] = "&mode=".doSaveCounters;
	$Form['out'] = "countForm";
	$Form['enabled'] = $GLOBALS['countstats']['enabled'];
	

	include("SetCountnersValueForm.php");


	tInsertContentHeader(Array('pagetitle'=>"Расход ресурсов за последние 10 месяцев. (м<sup>3</sup>)"),'page-title-var1');
	include("histogramm.php");	
	
	echo "<script>
			$('body').on('load',onCalcForm());
		</script>";
}
?>
