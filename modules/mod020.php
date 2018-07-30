<?php

function GetLastCounterValue($cid)
{
	$dbptr = $GLOBALS['dbptr'];
				
	$query = "SELECT valdate,value FROM livCountersValue WHERE counter = $cid AND dateof < DATE_SUB(NOW(),INTERVAL 1 MONTH) ORDER BY dateof DESC";
	$vals = $dbptr->LoadRow($query);
	return $vals;
				
}
//----------------------------------------------------------------------------------------------------------------------			
function GetCurrentCounterValues($offset=0,$count=0)
{
	$dbptr = $GLOBALS['dbptr'];
	
	if(!$count && !$offset) $post = "";
	else $post = "LIMIT $offset, $count";
	
	$query = "SELECT acc.id, acc.number,acc.location,cnt.service,cnt.id as countId,val.dateof,val.valdate,val.value,val.id as recId FROM livCountersValue val JOIN livCounters cnt on cnt.id = val.counter JOIN livComAccounts acc on acc.id=cnt.paccount WHERE status=1 $post";
	$vals = $dbptr->LoadData($query);
				
	return $vals;
}
//----------------------------------------------------------------------------------------------------------------------
function GetCurrentCounterValue($recID)
{
	$dbptr = $GLOBALS['dbptr'];
	$query = "SELECT acc.id, acc.number,acc.location,cnt.service,cnt.id as countId,val.dateof,val.valdate,val.value,val.id as recId FROM livCountersValue val JOIN livCounters cnt on cnt.id = val.counter JOIN livComAccounts acc on acc.id=cnt.paccount WHERE status=1 AND val.id=$recID";
	$vals = $dbptr->LoadRow($query);
				
	return $vals;
}
//----------------------------------------------------------------------------------------------------------------------
function valuesListOut($gofs,$gcnt,$allRec)
{	
	$pageNum = intval($gofs/20);
	$lastPage = intval($allRec/20);
		
	$vals = GetCurrentCounterValues($gofs,$gcnt);
	
	$out = "";

	
	$out .= "<div style='text-align: center;'>
		<button type='button' class='small-button' onclick='valuesListShow(0,$gcnt,$allRec)' ".(!$gofs ? "disabled":"")."><div class='button-icon button-icon-double_arr_left'></div></button>
		<button type='button' class='small-button' onclick='valuesListShow(".($gofs-20).",$gcnt,$allRec)' ".(!$gofs ? "disabled":"")."><div class='button-icon button-icon-arr_left'></div></button>
		<span>$pageNum / $lastPage</span>
		<button type='button' class='small-button' onclick='valuesListShow(".($gofs+20).",$gcnt,$allRec)' ".($pageNum==$lastPage ? "disabled":"")."><div class='button-icon button-icon-arr_right'></div></button>
		<button type='button' class='small-button' onclick='valuesListShow(".(($lastPage*20)-20).",$gcnt,$allRec)' ".($pageNum==$lastPage ? "disabled":"")."><div class='button-icon button-icon-double_arr_right'></div></button>
		</div>";
	
	$out .=  "<table border='1'><tr><th width='3%'>№ п/п</th><th width='5%'>ЛС</th><th>Адрес</th><th width='15%'>Номер счетчика</th><th width='8%'>Дата прошлых показаний</th><th width='8%'>Прошлые показания</th><th width='8%'>Дата текущих показаний</th><th width='8%'>Текущие показания</th><th width='8%'>Расход</th><th width='5%'>-/-</th></tr>";

	$ix = $gofs+1;
	$hw = 1;
	$cw = 1;
	$oldnumber = 0;
	foreach($vals as $key => $val){
		if($val['number']!=$oldnumber){
			$hw = 1;
			$cw = 1;
			$oldnumber = $val['number'];
		}
				
		$last = GetLastCounterValue($val['countId']);
		if($val['service']==500){
			 $cn = "Счетчик ХВС №".$cw++;
			 $ls = "style='background-color:#E0E0FF';";
		}
		if($val['service']==600){
			 $cn = "Счетчик ГВС №".$hw++;
			 $ls = "style='background-color:#FFE0E0';";
		}
		$rate = $val['value'] - $last['value'];
		$st = "";
		if($rate<0) $st = "style='background-color:red;'";
		if($rate>20) $st = "style='background-color:yellow;'";
		
		$out .=  "<tr $ls><td>$ix</td><td>".$val['number']."</td><td>".$val['location']."</td><td>$cn</td><td>".$last['valdate']."</td><td>".$last['value']."</td><td>".$val['valdate']."</td><td>".$val['value']."</td><td $st>".($rate)."</td>
			<td><button class='small-button' onclick='Commandsd(\"modules/mod020.php\",\"&command=".doEdit."&ajax=1&recid=".$val['recId']."\")'><div class='button-icon  button-icon-edit'></div></button></td>	
			</tr>";
		$ix++;		
	}
		
	$out .=  "</table>";
	
	return $out;
	
}

	
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : 0;
	$state = isset($_REQUEST['state']) ? $_REQUEST['state'] : 0;
	
	$user = GetCurrentUser();
	$result = Array('result'=>false,'message'=>'');
	
	
	
	
	if($user['id']){
		$accountID = $user['account'];
		$date = date("Y-m-d");	
		switch($command){
			case doShow:
			
				$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
				$amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 20;
				$allrec = isset($_REQUEST['allrec']) ? $_REQUEST['allrec'] : 0;
				
				$_SESSION['admcountshow']['$offset'] = $offset;
				$_SESSION['admcountshow']['$amount'] = $amount;
						
				$result['message'] .= valuesListOut($offset,$amount,$allrec);
				$result['result'] = true;
			break;
			case doSetCountState://Состояние приема счетчиков---------------------------------------
				$query = "UPDATE sysSettings SET value=$state WHERE name='count_enabled'";
				if(!$mysdb->Query($query)){
							$result['message'].="Ошибка<br>.$query";
							$err++;
				}
				else{
					if($state){
						$mysdb->Query("UPDATE sysSettings SET value=0 WHERE name='count_saved'");
						$mysdb->Query("UPDATE sysSettings SET value=0 WHERE name='count_cleared'");
					}
					$result['result'] = true;
					$result['reload'] = true;
				}		
			break;
			case doLoadCountValues:
				$result['message'] .= "Сохранение";
				$vals = GetCurrentCounterValues();
				$count = Array();
				foreach($vals as $key => $val){
					$c['id'] = $val['countId'];
					$c['date'] = $val['valdate'];
					$c['value'] = $val['value'];
					$count[$val['id']][] = $c;	
				}
				unset($vals);
				
				
				$text = "";
				$filename = "Показания_".date("Y.m").".txt";
				$fullname = "pub/cvalues/".$filename;
				foreach($count as $key => $val){
					$text .= $key.":";
					if(count($val)>2){
						$text.=$val[0]['date']."_".$val[0]['id']."_".$val[0]['value'].";";
						$text.=$val[1]['date']."_".$val[1]['id']."_".$val[1]['value'].";";
						if(isset($val[2])) $text.=$val[2]['date']."_".$val[2]['id']."_".$val[2]['value'].";";
						if(isset($val[3])) $text.=$val[3]['date']."_".$val[3]['id']."_".$val[3]['value'];		
					}	
					else{
						$text.=$val[0]['date']."_".$val[0]['id']."_".$val[0]['value'].";0;";
						$text.=$val[1]['date']."_".$val[1]['id']."_".$val[1]['value'].";0";	

					}
					$text.="\n";

				}
				
				$FILE = fopen("../".$fullname,"w");
				if($FILE){
					$res = fwrite($FILE,$text,strlen($text));
					fclose($FILE);
					
					
	//Сохраняем информацию о файле в БД------------------------------------------------------------
					$query = "SELECT id FROM livFiles WHERE filedesc='$filename' AND type=6";
					$val = $mysdb->LoadRow($query);
					
					if(!is_array($val)){
						$query = "INSERT INTO livFiles (owner,filename,filedesc,link,type,flag) VALUES(0,'$filename','$filename','$fullname',6,0)";	
						if(!$mysdb->Query($query)){
								$result['message'].="Ошибка сохранения информации о файле.<br>";
						}
					}
			
					$result['message'] .= "Успешно записано в файл ".$filename;
					$result['result'] = true;
					$result['reload'] = true;
					$result['file'] = $fullname;
					
					$mysdb->Query("UPDATE sysSettings SET value=1 WHERE name='count_saved'");
			
				}
			break;
			case doClearCountValues:
				$tmp = $mysdb->LoadRow("SELECT value FROM sysSettings WHERE name='count_saved'");
				if($tmp['value']==1){
					if($mysdb->Query("UPDATE livCountersValue SET status=0 WHERE status=1")){
						$mysdb->Query("UPDATE sysSettings SET value=1 WHERE name='count_cleared'");
						$result['result'] = true;
						$result['reload'] = true;
					}		
				}
				else{
					$result['message'] .= "Показания счетчиков не сохранены, очистка невозможна";
					$result['result'] = true;
					$result['reload'] = true;
				}
			break;
			case doEdit:
				$id = isset($_REQUEST['recid']) ? $_REQUEST['recid'] : 0;
				if($id){
					$Form['cur'] = GetCurrentCounterValue($id);			
					$Form['last'] = GetLastCounterValue($Form['cur']['countId']);
					
					$Form['handler'] = "modules/mod020.php";
					$Form['arg'] = "&command=".doSave."&recid=".$id;
					
					$result['message'] .=  corePutForm("EditCountValue.php",$Form); 
					
					$cn = (($Form['cur']['service'] == 500) ? "ХВС":"ГВС");
					$result['header'] = "Редактирование показаний счетчика ".($cn)." в квартире ".$Form['cur']['location']."(".$Form['cur']['number'].")";
					$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
					$result['user'] = $user['name'];
					$result['useform'] = true;	
					$result['result'] = true;
				}
			break;
			case doSave:
				$id = isset($_REQUEST['recid']) ? $_REQUEST['recid'] : 0;
				if($id){
					$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
					
					$vals = json_decode($data,true);
					$value = $vals['trueValue'];

					$query = "UPDATE livCountersValue SET value='$value' WHERE id=$id";
					
					if($dbptr->Query($query)){
						$result['message'] .= "Сохранено успешно. <br>";//$query;//print_r($vals,true);
						$result['reload'] = true;
						$result['result'] = true;
								
					}
					else{
						$result['reload'] = false;
						$result['message'] .= "Ошибка сохранения<br>".$query;
					}
				}		
			break;
			
		
		}
		
		
		unset($_REQUEST['ajax']);
		unset($_REQUEST['mode']);
		unset($_REQUEST['data']);
	}
		
	echo json_encode($result);
		
	
}
else{
	$countStats = isset($GLOBALS['countstats']) ? $GLOBALS['countstats'] : Array('enabled'=>false,'arc_loaded'=>false,'arc_cleared'=>false);
	
	$dbptr = $GLOBALS['dbptr'];
	$tmp = $dbptr->LoadRow("SELECT COUNT(id) as amount FROM livCountersValue WHERE status=1");
	$allRec = $tmp['amount'];
	unset($tmp);
	//$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	//$amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 20;
	//echo "<pre>";
	//print_r($_SESSION['admcountshow']);
	//echo "</pre>";
	
	
	$offset = isset($_SESSION['admcountshow']['$offset']) ? $_SESSION['admcountshow']['$offset']:0;
	$amount = isset($_SESSION['admcountshow']['$amount']) ? $_SESSION['admcountshow']['$amount']:20;
	$pageNum = intval($offset/20);
	
	$numRec = "записи с $offset по ".($offset + $amount). " из ".$allRec;	
	//$numRec="";
	
	/*echo "<pre>";
	print_r($countStats);
	echo "</pre>";*/
?>

<br>
<form class='input-panel adm1-panel' method='POST'  id='adm1Form'>
<fieldset>
	<legend>Действия с Индивидуальными приборами учета</legend>
	<fieldset>
	<legend><?php echo ($countStats['enabled'] ? "Прием показаний активен" : "Прием показаний отключен"); ?></legend>
	<div>
		<label for='btt-stop-getcounters'>Остановить прием показаний ИПУ</label>
		<button type='button' class='normal-button' id='btt-stop-getcounters' <?php echo ($countStats['enabled'] ? "" : "disabled"); ?> onclick='countGet(false);'>Остановить</button>
	</div>
	<div>
		<label for='btt-start-getcounters'>Запустить прием показаний ИПУ</label>
		<button type='button' class='normal-button' id='btt-start-getcounters' <?php echo ($countStats['enabled'] ? "disabled" : ""); ?> onclick='countGet(true);'>Запуск</button>
	</div>
	</fieldset>
	<hr>
	<fieldset>
	<div>
		<label for='btt-getcounters'>Скачать архив показаний ИПУ</label>
		<button type='button' class='normal-button' id='btt-getcounters' <?php echo (($countStats['enabled'] || $countStats['arc_cleared']) ? "disabled" : ""); ?> onclick='countSave();'>Скачать</button>
	</div>
	<div>
		<label for='btt-clearcounters'>Очистить архив показаний ИПУ</label>
		<button type='button' class='normal-button' id='btt-clearcounters' <?php echo (($countStats['enabled'] || !$countStats['arc_loaded'] || $countStats['arc_cleared']) ? "disabled" : ""); ?> onclick='countClear();'>Очистить</button>
	</div>
	</fieldset>
</fieldset>
</form>
<div class='input-panel adm1-panel'>
<fieldset>
	<legend id='valListHeader'></legend>
	<div id='valListOut'></div>
	
	<script>
		$('body').on('load',valuesListShow(<?php echo $offset.",".$amount.",".$allRec; ?>));
	
		function valuesListShow(offset,amount,allrec)
		{
			com = "command=1&offset=" + offset + "&amount=" + amount + "&allrec=" + allrec + "&ajax=1";
			ShowSpinner('valListOut');
			SendRequest("modules/mod020.php",com,function(req){		
			if(req.responseText.length > 0){
				try{
					obj = JSON.parse(req.responseText);
		        	 if(obj['result'] == true){	
		        	 	$('#valListOut').html(obj['message']);	 
		        	 	$('#valListHeader').html("Переданные показания приборов учета c " + offset + " по " + (offset + 20) + " из " + allrec + " записей");  	
						return true;		
					} 
		       	}    
		        catch (CatchException){
		        	 console.log(req.responseText);
		        }						
			}
		});
		}
	</script>	
</fieldset>
</div>

<?php
$Files = Array('filelist'=>'list','show'=>true,'type'=>6,'owner'=>0);			
include("files.php");
?>


<script>
	
//-------------------------------------------------------------------
	function countGet(stat)
	{
		com = "command=20&state="+stat+"&ajax=1";			
		SendRequest("modules/mod020.php",com,function(req){
		if(req.responseText.length > 0){
			try{
	        	 obj = JSON.parse(req.responseText);
	        	 	if(obj['result']==true) location.reload();					 		   	
					return true;		
	       	}    
	        catch (CatchException){
	        	 console.log(req.responseText);	
	        }					
		}
	});
	}
	
//-------------------------------------------------------------------	
	function countSave()
	{
		com = "command=21&ajax=1";
		SendRequest("modules/mod020.php",com,function(req){
		if(req.responseText.length > 0){
			
			try{
	        	obj = JSON.parse(req.responseText); 
	        	if(obj['result']==true){	    				       	
					window.open(obj['file']); 
					location.reload();		 		   	
					return true;
				}		
	       	}    
	        catch (CatchException){
	        	 console.log(req.responseText);		
	        }				
		}
	});
	}
//-------------------------------------------------------------------	
	function countClear()
	{
		com = "command=22&ajax=1";
			
		SendRequest("modules/mod020.php",com,function(req){
		if(req.responseText.length > 0){
			try{
	        	 obj = JSON.parse(req.responseText);
	        	 	if(obj['result']==true){
	        	 		location.reload();					 		   	
						return true;		
					}
	       	}    
	        catch (CatchException){
	        	console.log(req.responseText);	
	        }					
		}
	});
	}

</script>

<?php

}
?>
