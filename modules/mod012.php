<?php
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");
 
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : Array();
	
	$user = GetCurrentUser();
	$accountID = $user['account'];
	$date = date("Y-m-d");
	$month = date("Y-m-01");
	
	$result = Array('result'=>true,'message'=>'');
	
	$tmp  =$dbptr->LoadRow("SELECT billdate FROM livComSaldo ORDER BY billdate DESC");
	$period = $tmp['billdate'];
	unset($tmp);
	
	$AccountInfo = $dbptr->LoadRow("SELECT acc.number as accNumber, location,acc.root as buildID,employer,registered,square,aptnumber,street,build.number as housenumber FROM livComAccounts acc JOIN livBuilding build on build.id=acc.root WHERE acc.id=$accountID");

	$srvcng = Array('101'=>'110','115'=>'1600','175'=>'175','176'=>'176','177'=>'177','178'=>'178','179'=>'179','180'=>'180');
	$BillTable = Array();
	$BillTable['account'] = $AccountInfo;
	$BillTable['account']['qr'] = "ST00011|Name=ООО&nbsp;'Ваш&nbsp;управдом&nbsp;плюс'|PersonalAcc=40702810420580000005|BankName=Новосибирский&nbsp;филиал&nbsp;№2&nbsp;ПАО&nbsp;&nbsp;'БИНБАНК'&nbsp;в&nbsp;г.&nbsp;Новосибирске|BIC=045004884|CorrespAcc=30101810550040000884|PayerINN=2445003218|Sum=";
	$saldo = $dbptr->LoadData("SELECT * FROM livComSaldo WHERE account=$accountID");
	$accrual = $dbptr->LoadData("SELECT * FROM livComAccrual WHERE account=$accountID");
	$recalc = $dbptr->LoadData("SELECT * FROM livComRecalc WHERE account=$accountID");
	$pay = $dbptr->LoadData("SELECT * FROM livComPay WHERE account=$accountID");
	
	$Hidden = true;
	
	coreHideInclude("mod22.php");
	coreHideInclude("mod2.php");
	$tmp = buildGetList($AccountInfo['buildID']);
	
	$build['allsquare'] = isset($tmp['property']['Площадь']) ? $tmp['property']['Площадь'] : 0;
	$build['unlivesquare'] = isset($tmp['property']['Площадь нежилых помещений']) ? $tmp['property']['Площадь нежилых помещений'] : 0;
	$build['sharesuqare'] = isset($tmp['property']['Площадь общедомового имущества']) ? $tmp['property']['Площадь общедомового имущества'] : 0;
	unset($tmp);

	$Services = Array();
	
	$saldosum = 0;
	$acrsum = 0;
	$recalcsum = 0;
	$paysum = 0;
	foreach($saldo as $key => $val){
		$srv = isset($srvcng[$val['service']]) ? $srvcng[$val['service']] : intval($val['service']/100)*100;
		
		
		if(isset($Services[$srv]['saldo'])) $Services[$srv]['saldo'] += $val['bill'];
		else								$Services[$srv]['saldo'] = $val['bill'];
		
		if($srv) $saldosum+=$val['bill'];
	}
	foreach($accrual as $key => $val){
		$srv = isset($srvcng[$val['service']]) ? $srvcng[$val['service']] : intval($val['service']/100)*100;
		if(isset($Services[$srv]['accrual'])) $Services[$srv]['accrual'] += $val['bill'];
		else													$Services[$srv]['accrual'] = $val['bill'];
		
		$acrsum +=  $val['bill'];
	}
	foreach($recalc as $key => $val){
		$srv = isset($srvcng[$val['service']]) ? $srvcng[$val['service']] : intval($val['service']/100)*100;
		if(isset($Services[$srv]['recalc'])) $Services[$srv]['recalc'] += $val['bill'];
		else													$Services[$srv]['recalc'] = $val['bill'];
		
		$recalcsum +=  $val['bill'];
	}
	foreach($pay as $key => $val){
		$srv = isset($srvcng[$val['service']]) ? $srvcng[$val['service']] : intval($val['service']/100)*100;
		if(isset($Services[$srv]['pay'])) $Services[$srv]['pay'] += $val['bill'];
		else													$Services[$srv]['pay'] = $val['bill'];
		
		$paysum +=  $val['bill'];
	}
	unset($saldo,$accrual,$recalc,$pay);
	
	foreach($Services as $srv => $val){
		if($srv){	
			$tmp = loadServices($srv,$build);
			if(isset($tmp['name']) && $tmp['name']!=""){
				$BillTable['bill'][$srv] = $val;
				$BillTable['bill'][$srv]['info']['name'] = $tmp['name'];
				$BillTable['bill'][$srv]['info']['volume'] = isset($tmp['prices']['volume'])? $tmp['prices']['volume'] : 0;
				$BillTable['bill'][$srv]['info']['price'] = isset($tmp['prices']['price'])? $tmp['prices']['price'] : 0;
				$BillTable['bill'][$srv]['info']['calcprice'] = isset($tmp['prices']['calcprice'])? $tmp['prices']['calcprice'] : 0;
				$BillTable['bill'][$srv]['info']['measure'] = isset($tmp['prices']['measureunit'])? $tmp['prices']['measureunit'] : 0;
				//$BillTable['bill'][$srv]['info']['data'] = $tmp;
				
				if(isset($Services[$srv]['accrual']))
					$BillTable['bill'][$srv]['info']['usedvolume'] = round($Services[$srv]['accrual']/$BillTable['bill'][$srv]['info']['price'],2,PHP_ROUND_HALF_ODD);
				
				
			}
			unset($tmp);
		}
	}
	unset($Services);
	
	
	$topay = $saldosum + ($acrsum+$recalcsum) - $paysum;
	$BillTable['account']['qr'] .= ($topay*100)."|persAcc=".$BillTable['account']['accNumber'];
	$BillTable['period'] = $period;
	
	$result['message']=corePutForm("billtable.php",$BillTable);

	unset($_REQUEST['id']);
	echo json_encode($result);
	unset($result);
}
else{
?>
	<br>
	<div id='billtable-container'>	
	</div>
	<p style='text-align: center;font:bold 12px arial'>Для вывода окна с таблицей, разрешите всплывающие окна в вашем браузере для нашего сайта</p>
	<div style='text-align: right;padding:10px;'>
		<button type='button' class='normal-button' onclick='makeBill(<?php echo $accountID; ?>,true);'>Вывести на печать</button>
	</div>

	<script>
		$('body').on('load',makeBill(<?php echo $accountID; ?>,false));


	function makeBill(accountID,target){
		SendRequest('modules/mod012.php','ajax=1&id='+accountID,function(req){
			if(req.responseText.length > 0){
				try{
		        	 obj = JSON.parse(req.responseText);
		        	 if(obj['result'] == true){	
		        	 	if(!target) $('#billtable-container').html(obj['message']);
		        	 	else{
							var w = window.open();
							w.document.writeln(obj['message']);
						}  	
						return true;		
					} 
					else{
						jAlert("Ошибка","Ошибка построения отчета");
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

