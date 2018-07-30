<?php

//Подготовка данных---------------------------------------------------------------------------------------------------
$srvImages = Array('800'=>'./templates/base/images/icon_srv5.png','600'=>'./templates/base/images/icon_srv4.png','100'=>'./templates/base/images/icon_srv2.png','1200'=>'./templates/base/images/icon_srv1.png','1000'=>'./templates/base/images/icon_srv0.png','2000'=>'./templates/base/images/icon_srv3.png');
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : doNope;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$sid = isset($_REQUEST['srvid']) ? $_REQUEST['srvid'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	

	$user = GetCurrentUser();
	
	$result = Array('result'=>false,'message'=>'','useform'=>false);
	
	$dbptr = $GLOBALS['dbptr'];
	
	switch($command){
		case doEdit:
			$srv = $dbptr->LoadRow("SELECT * FROM livServices WHERE id=$id");
		case doAdd:
			$Form['id'] 		= $id;
			$Form['name'] 		= isset($srv['name'])		? $srv['name'] 	: date("Y-m-d");
			$Form['root'] 		= isset($srv['root'])? $srv['root'] : (isset($_REQUEST['rootid']) ? intval($_REQUEST['rootid']) : 0);
			$Form['base'] 		= isset($srv['baseservice'])	? $srv['baseservice'] 	: 0;
			$Form['calcsrv'] 	= isset($srv['calc'])	? $srv['calc'] 	: 0;
			$Form['unit'] 		= isset($srv['measureunit']) 		? $srv['measureunit'] 		: "";
			$Form['calcunit'] 	= isset($srv['calcunit']) 			? $srv['calcunit'] 		: "";
			$Form['interval'] 	= isset($srv['interval']) 			? $srv['interval'] 		: "";
			$Form['notice']		= isset($srv['notice']) 	? $srv['notice'] 	: "";
			$Form['services'] = $dbptr->LoadData("SELECT id,name FROM livServices WHERE id<>$id",true,'id','name');
			$Form['calctypes'] = coreLoadProperty("calcTypes");
			$Form['prices'] =  $dbptr->LoadData("SELECT id as pid,DATE_FORMAT(datebegin, '%d.%m.%Y') as datebegin,DATE_FORMAT(dateend, '%d.%m.%Y') as dateend,price,volume,provider,measureunit,volmeasureunit FROM livPrice WHERE sindex=$id");
			$Form['providers'] = $dbptr->LoadData("SELECT id,name FROM livOrganizations WHERE type IN(1,3)",true,'id','name');
		
			
			
			$Form['handler'] = "modules/mod22.php";
			$Form['arg'] = "&command=".doSave."&id=".$id;
			$result['message'] = corePutForm("EditServiceForm.php",$Form);

			$result['header'] = "Услуга: ".$Form['name'];
			$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
			$result['user'] = $user['name'];
			$result['useform'] = true;	
			$result['result'] = true;
		
		break;
		case doSave:
			$vals = json_decode($data,true);
			
			$serviceid 		= intval($vals['service-id']);
			$srvname 		= $vals['srv-name'];
			$srvnotice		= $vals['srv-notice'];
			$srvinterval	= $vals['srv-interval'];
			$srvmsr			= $vals['srv-measure'];
			$srvcalcsmsr	= $vals['srv-calcmeasure'];
			$srvroot		= intval($vals['srv-root']);
			$srvbase		= intval($vals['srv-base']);
			$srvcalc		= intval($vals['srv-calctype']);
			
			if($id){
				$query = "UPDATE livServices SET root=$srvroot,name='$srvname',baseservice=$srvbase,livServices.interval='$srvinterval',calc=$srvcalc,measureunit='$srvmsr',calcunit='$srvcalcsmsr',notice='$srvnotice' WHERE id=$id";
			}
			else{
				$query="INSERT INTO livServices (root,name,baseservice,livServices.interval,calc,measureunit,calcunit,notice)
					 VALUES($srvroot,'$srvname',$srvbase,'$srvinterval',$srvcalc,'$srvmsr','$srvcalcsmsr','$srvnotice')";
		
			}
		
			if($dbptr->Query($query)){	 		 
				$result['result'] = true;
				$result['reload'] = true;
			}
			else{
				
			}
			
		break;
		case doDelete:	
			$childs = $dbptr->LoadData("SELECT id FROM livServices WHERE root=$id");
			if(is_array($childs) && count($childs)){
				$result['reload'] = false;
				$result['result'] = false;
				$result['message'] = "У данной услуги есть подчиненные услуги, удаление невозможно";	
			}
			else{
				//Удаляем услугу--------------------------------------------
				$query = "DELETE FROM livServices WHERE id=$id";
				if($dbptr->Query($query)){
				//Удаляем привязанные тарифы--------------------------------
					$query = "DELETE FROM livPrice WHERE sindex=$id";
					if($dbptr->Query($query)){
						$result['reload'] = true;
						$result['result'] = true;
					}	
					else{
						 $result['result'] = false;
						 $result['message'] = "Ошибка удаления записи из БД";
					}
				}
				else{
					$result['result'] = false;
					$result['message'] = "Ошибка удаления записи из БД";
				}
			}					
		break;
		
		case doEditPrice:
			$pid = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : 0; 
			$price = $dbptr->LoadRow("SELECT * FROM livPrice WHERE id=$pid");
		case doAddPrice:
			if(!isset($pid)) $pid = 0;
			$Form['id'] 		= $id;
			$Form['price-id'] = isset($price['id']) ? $price['id'] : 0;
			$Form['price-date-beg'] = isset($price['datebegin']) ? $price['datebegin'] : date("Y-m-01");
			$Form['price-date-end'] = isset($price['dateend']) ? $price['dateend'] : date("Y-12-31");
			$Form['price'] = isset($price['price']) ? $price['price'] : 0.0;
			$Form['price-volume'] = isset($price['volume']) ? $price['volume'] : 0.0;
			$Form['price-measure'] = isset($price['measureunit']) ? $price['measureunit'] : "";
			$Form['price-vol-measure'] = isset($price['volmeasureunit']) ? $price['volmeasureunit'] : "";
			$Form['price-description'] = isset($price['description']) ? $price['description'] : "";
			$Form['price-provider'] = isset($price['provider']) ? $price['provider'] : 0;
			$Form['price-service'] = $pid;
			$Form['providers'] = $dbptr->LoadData("SELECT id,name FROM livOrganizations WHERE type IN(1,3)",true,'id','name');
			
			
			
			
		
			$Form['handler'] = "modules/mod22.php";
			$Form['arg'] = "&pid=".$pid."&id=".$id;
			$Form['prices'] =  $dbptr->LoadData("SELECT * FROM livPrice WHERE sindex=$id");
			$Form['addprice'] = true;
			$result['message'] =  corePutForm("EditServicePriceForm.php",$Form);
			$result['user'] = $user['name'];
			$result['useform'] = false;	
			$result['output'] = 'prices-container';	
			$result['result'] = true;
		break;
		case doSavePrice:
			$pid = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : 0; 
			$vals = json_decode($data,true);
			
			$serviceid 		= intval($vals['service-id']);
			$priceid 		= intval($vals['price-id']); 
			$pricebeg 		= $vals['price-date-beg'];
			$priceend 		= $vals['price-date-end'];
			$price			= floatval($vals['price']);   
			$pricevol 		= floatval($vals['price-volume']);
			$pricemsr 		= $vals['price-measureunit']; 
			$pricevolmsr 	= $vals['price-vol-measureunit'];  
			$priceprov 		= intval($vals['price-provider']); 
			$pricedscr 		= $vals['price-description']; 
			
			
			if($priceid){
				$query = "UPDATE livPrice SET measureunit='$pricemsr',datebegin='$pricebeg',price=$price,volume=$pricevol,dateend='$priceend',volmeasureunit='$pricevolmsr',description='$pricedscr',provider=$priceprov   WHERE id=$priceid";
			}
			else{
				$query = "INSERT INTO livPrice (sindex,measureunit,datebegin,price,volume,dateend,volmeasureunit,description,provider) 
					VALUES($serviceid,'$pricemsr','$pricebeg',$price,$pricevol,'$priceend','$pricevolmsr','$pricedscr',$priceprov)";
			}
			
			if($dbptr->Query($query)){	 		 
				$Form['id'] 		= $id;
				$Form['handler'] = "modules/mod22.php";
				$Form['arg'] = "";
				$Form['prices'] =  $dbptr->LoadData("SELECT id as pid,DATE_FORMAT(datebegin, '%d.%m.%Y') as datebegin,DATE_FORMAT(dateend, '%d.%m.%Y') as dateend,price,volume,provider,measureunit,volmeasureunit FROM livPrice WHERE sindex=$id");
				$Form['providers'] = $dbptr->LoadData("SELECT id,name FROM livOrganizations WHERE type IN(1,3)",true,'id','name');
				 
				$result['message'] =  corePutForm("EditServicePriceForm.php",$Form);
				$result['useform'] = false;	
				$result['output'] = 'prices-container';	
				$result['result'] = true;
			}
			else{
				
			}		
		break;
		case doShowPrice:
				$Form['id'] 		= $id;
				$Form['handler'] = "modules/mod22.php";
				$Form['arg'] = "";
				$Form['prices'] =  $dbptr->LoadData("SELECT id as pid,DATE_FORMAT(datebegin, '%d.%m.%Y') as datebegin,DATE_FORMAT(dateend, '%d.%m.%Y') as dateend,price,volume,provider,measureunit,volmeasureunit FROM livPrice WHERE sindex=$id");
				$Form['providers'] = $dbptr->LoadData("SELECT id,name FROM livOrganizations WHERE type IN(1,3)",true,'id','name');
				$result['message'] =  corePutForm("EditServicePriceForm.php",$Form);
				$result['useform'] = false;	
				$result['output'] = 'prices-container';	
				$result['result'] = true;
		break;
		case doDeletePrice:
				$pid = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : 0; 
				$query = "DELETE FROM livPrice WHERE id=$pid";
				if($dbptr->Query($query)){
					$result['reload'] = false;
					$result['result'] = true;
					
					$Form['id'] 		= $id;
					$Form['handler'] = "modules/mod22.php";
					$Form['arg'] = "";
					$Form['prices'] =  $dbptr->LoadData("SELECT id as pid,DATE_FORMAT(datebegin, '%d.%m.%Y') as datebegin,DATE_FORMAT(dateend, '%d.%m.%Y') as dateend,price,volume,provider,measureunit,volmeasureunit FROM livPrice WHERE sindex=$id");
					$Form['providers'] = $dbptr->LoadData("SELECT id,name FROM livOrganizations WHERE type IN(1,3)",true,'id','name');
					$result['message'] =  corePutForm("EditServicePriceForm.php",$Form);
					$result['useform'] = false;	
					$result['output'] = 'prices-container';	
					$result['result'] = true;
				}
				else{
					$result['reload'] = false;
					$result['output'] = 'prices-container';	
					$result['result'] = false;
					$result['message'] = 'Ошибка удаления цены';
				}
		break;
	}	
	unset($_REQUEST['ajax']);
	unset($_REQUEST['mode']);
	unset($_REQUEST['data']);
	
	echo json_encode($result);
}
else{

//Вывод данных--------------------------------------------------------------------------------------------------------
//Входные параметры для включения формы в другие разделы--------------------------------------------------
if(!isset($Hidden)) $Hidden = false;
if(!$Hidden){
	if(isset($Parametrs)){
		$com = isset($Parametrs['command'])?$Parametrs['command']:$com;
		$allsquare = isset($Parametrs['allsquare'])?$Parametrs['allsquare']:0;
		$sharedsquare = isset($Parametrs['sharesuqare'])?$Parametrs['sharesuqare']:0;
		$unlivesquare = isset($Parametrs['unlivesuqare'])?$Parametrs['unlivesuqare']:0;	
		$srvid = intval($GLOBALS['command']['add']);
		if(!$srvid) unset($srvid);
		else{
			$id = $srvid;
			$com = doShow;
		}
	}
	else $Parametrs = NULL;

//-----------------------------------------------------------------------------------------------------------
	switch($com){
		case doList:
		$user = GetCurrentUser();
			$cdate = getdate();
			if(!isset($Parametrs['owner']))  tInsertContentHeader(Array('pagetitle'=>"Тарифы на ".$cdate['year']." год."));	
			
			$services = loadServices(0,$Parametrs);	
			$out=Array();
			foreach($services as $key => $val){
				if($val['calc'] && isset($val['prices']) && $val['prices']['calcprice']>0) $out['header'] = $val['name'].": ".$val['prices']['calcprice']." руб. за ".$val['measureunit'];
				else 									$out['header'] = $val['name'];
				
				$out['icon'] = $srvImages[$key];
		
				$out['out'] = $val['out'];
				$out['id'] = $val['id'];
				
				$out['handler'] = 'modules/mod22.php';
				
				
				/*if($user['right']>=100){
				echo "<pre>";
				echo $out['id'];
				//print_r($services);
				echo "</pre>";	
				}*/
				
				insertServiceBlock($val['out'],$out,$Parametrs);
				
				tInsertHierarchyBlock($out,'services');
			}
			
	
			/*if($user['right']>=100){
			echo "<pre>";
			print_r($services);
			echo "</pre>";	
			}*/
		break;
		case doShow:	
			$service = loadServices($id,$Parametrs);	
			$tt = ($service['root']%100);
			if(!$Parametrs){
				if($tt) 	$backlink = makeLink(-1,-1,-1,-1,-1,$service['root']);
				else	 	$backlink = makeLink(-1,-1,-1,doList,0,0);
			}
			else{
				if($tt) 	$backlink = makeLink(-1,-1,-1,-1,-1,-1,"add=".$service['root']);
				else	 	$backlink = makeLink(-1,-1,-1,-1,-1,-1,"add=0");	
			}



			tInsertContentHeader(Array('pagetitle'=>$service['name'],'backbutton'=>$backlink));
			
		
				if($service['calc'] && isset($service['price'])) $out['header'] = $service['name'].": ".$service['price']." руб. за ".$service['measureunit'];
				else 									$out['header'] = $service['name'];
				
				$grp = intval($service['id']/100)*100;
				
				$out['icon'] = $srvImages[$grp];
				$out['out'] = $service['out'];
				$out['id'] = $service['id'];		
				$out['handler'] = 'modules/mod22.php';	
				
				insertServiceBlock($service['out'],$out,$Parametrs);

				tInsertHierarchyBlock($out,'services',true);
			
			
			/*echo "<pre>";
			//print_r($Parametrs);
			print_r($service);
			echo "</pre>";*/
		break;
	}






	unset($tmp);
	tInsertContentFooter();

}
}


/*********************************************************************************************************
* 							PREPARE SERVICE DATA FOR OUTPUT
**********************************************************************************************************/

function insertServiceBlock($data,&$out,$Parametrs)
{
	foreach($data as $skey => $sval){
			if(count($sval['out'])) $multsrv = true;
			else					$multsrv = false;
			$price = isset($sval['prices']) ? $sval['prices'] : Array('datebegin'=>"",'dateend'=>"",'price'=>'','volume'=>'','provider'=>'','description'=>'') ;
			
			
			$oh = $sval['name'].((isset($price['calcprice']) && $price['calcprice']>0) ? ": ".$price['calcprice']." руб. ".(($sval['calcunit']!="") ? $sval['calcunit'] : (isset($price['volmeasureunit']) ? $price['volmeasureunit']:"")):"");
			
			
			if($multsrv){
					if(!$Parametrs)	$out['out'][$skey]['header'] = "<a href='".makeLink(2,chapterPrices,0,doShow,0,$sval['id'])."'>$oh</a>";
					else			$out['out'][$skey]['header'] = "<a href='".makeLink(-1,-1,-1,-1,-1,-1,"add=".$sval['id'])."'>$oh</a>";
				}
			else 				$out['out'][$skey]['header'] = $oh;



			if(isset($price['price']) && $price['price']){
				$out['out'][$skey]['text'] = "Период действия: ".$price['datebegin']." - ".$price['dateend']."<br>";
				if($price['price']) 	$out['out'][$skey]['text']  .= "Цена: ".$price['price']." руб. за ".$price['measureunit']."<br>";
				if($price['volume'])	$out['out'][$skey]['text']  .= "Норматив: ".$price['volume']." ".$price['measureunit']." ".$price['volmeasureunit']."<br>";
				$prc = $price['calcprice'];
				if($prc && $price['volume'])	$out['out'][$skey]['text']  .= "Стоимость: ".$prc." руб. ".(($sval['calcunit']!="") ? $sval['calcunit'] : $price['volmeasureunit'])."<br>";
				
				$out['out'][$skey]['text'] .= "Поставщик: ".$price['providername']."<br>";
				$out['out'][$skey]['text'] .= "Примечание: ";
				if($multsrv) $out['out'][$skey]['text'] .= "<br><i>&nbsp;&nbsp;Составная услуга, кликните на названии для получения подробной информации.</i>";
				if($sval['notice']!="")	$out['out'][$skey]['text'] .= "<br><i>&nbsp;&nbsp;".$sval['notice']."</i>";	
				$out['out'][$skey]['text'] .= "<br><i>&nbsp;&nbsp;".$price['description']."</i>";				
			
			}
			else{
				$out['out'][$skey]['text']="";
				if($multsrv) $out['out'][$skey]['text'] = "Составная услуга, кликните на названии для получения подробной информации.";
				else{
					if($sval['interval']!="") $out['out'][$skey]['text'] = "Период действия: ".$sval['interval']."<br>";
					if(isset($price['description']) && $price['description']!="") $out['out'][$skey]['text'] = "Примечание: <i><br>&nbsp;&nbsp;".$price['description']."</i><br>";
				}
			}
			

		}
}



/**************************************************************************************************************

												ФУНКЦИИ

***************************************************************************************************************/
function loadServices($id=0,$Parametrs)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	

	if($id){
		$query = "SELECT * FROM livServices WHERE id=$id";	
		$srv = $dbptr->LoadRow($query);
		
		$query = "SELECT * FROM livServices ORDER BY 'index' DESC";	
		$tmp = $dbptr->LoadData($query);
		
			$t2 = Array();
			loadSecondSerices($tmp,$srv['id'],$t2,$Parametrs);
			
			$dep = ($srv['baseservice']) ? loadServices(intval($srv['baseservice']),$Parametrs) : Array(1,2,3);
			
			$prices = getCurrentPrices($srv['id'],$srv['calc'],$Parametrs,$dep);
			$srv['prices'] = $prices;
			
			$sumprice = 0;
			if(isset($srv['calc']) && $srv['calc']==1){	
				foreach($t2 as $key=>$val){
					$sumprice +=$val['prices']['calcprice']; 		 
				}
						
				$srv['prices']['calcprice'] = $sumprice;
			}
			if(isset($srv['prices']['price']) && $srv['prices']['price'] == 0 && isset($srv['prices']['volume']) && $srv['prices']['volume']) $srv['prices']['price'] = round($sumprice/$srv['prices']['volume'],2);
					
			$srv['out'] = $t2;
		
		unset($tmp);
		return $srv;
	
	}
	else{
		

		$query = "SELECT * FROM livServices ORDER BY 'index' DESC";	
		$tmp = $dbptr->LoadData($query);
		
		$out = Array();
		$t2 = Array();
		
		
		for($a=0;$a<count($tmp);$a++){
			if($tmp[$a]['root'] < 0){
				$n = $tmp[$a]['id'];
				$out[$n]['id'] = $tmp[$a]['id'];
				$out[$n]['name'] = $tmp[$a]['name'];
				$out[$n]['measureunit'] = $tmp[$a]['measureunit'];
				$out[$n]['calc'] =  $tmp[$a]['calc'];
			
	
				loadSecondSerices($tmp,$n,$t2,$Parametrs);
				
				$sumprice = 0;
				if($out[$n]['calc']==1){	
					foreach($t2 as $key=>$val){
							   	$sumprice +=$val['prices']['calcprice']; 		 
					}
					
					$out[$n]['prices']['calcprice'] = $sumprice;
				}
				
				
				$out[$n]['out'] = $t2;
				ksort($out[$n]['out']);
				unset($t2);		
			}	
		}
	}

	unset($tmp);
	return $out;
}

//------------------------------------------------------------------------
function loadSecondSerices($arr,$key,&$out,$Parametrs)
{
	$pprice = 0;
	
	for($b=0;$b<count($arr);$b++){
		if($arr[$b]['root'] == $key){
			$n = $arr[$b]['id'];
			
			$out[$n] = $arr[$b];
			
			$owner = isset($Parametrs['owner'])? $Parametrs['owner'] : 0;
			
			//$dep = (($out[$n]['calc'] == 4 || $out[$n]['calc'] == 5) && $out[$n]['baseservice']) ? $out[intval($out[$n]['baseservice'])] : Array();
			$dep = ($out[$n]['baseservice']) ? loadServices(intval($out[$n]['baseservice']),$Parametrs) : Array(1,2,3);
			
			
			$prices = getCurrentPrices($n,$out[$n]['calc'],$Parametrs,$dep);
			$out[$n]['prices'] = $prices;
				
			loadSecondSerices($arr,$n,$out[$n]['out'],$Parametrs);	
			
			$sumprice = 0;
			if($out[$n]['calc']==1){	
				foreach($out[$n]['out'] as $skey=>$val){
					$sumprice +=$val['prices']['calcprice']; 		 
				}
					
				$out[$n]['prices']['calcprice'] = $sumprice;
			}
			
			
			
								
		}
	}	
}
//------------------------------------------------------------------------
function getCurrentPrices($index,$calctype,$Parametrs,$dep)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$owner = isset($Parametrs['owner'])? $Parametrs['owner'] : 0;
	if($owner) 	$ownercheck = " AND (owner=$owner OR owner=0) ";
	else 		$ownercheck = "";
	
	$query = "	SELECT price.id,measureunit,DATE_FORMAT(datebegin, '%d.%m.%Y') as datebegin,DATE_FORMAT(dateend, '%d.%m.%Y') as dateend,
				price,volume,volmeasureunit,price.description,provider,org.name as providername,owner
				FROM livPrice price
				LEFT JOIN livOrganizations org on org.id = price.provider
				WHERE sindex = $index AND NOW() >= datebegin AND NOW() <= dateend $ownercheck ORDER BY datebegin";	

	$price = $dbptr->LoadRow($query);
	
	if(!is_array($price)) return Array();
	
	
	$vol = $price['volume'];
	$bill = $price['price'];
	$t = $bill;
	if($Parametrs){
		$allsquare = round($Parametrs['allsquare'],2);
		$sharedsquare = round($Parametrs['sharesuqare'],2);
	}
	
	if(!isset($price['description'] )) $price['description']  = "";
	
	switch($calctype){
		case 0:
			if($vol==0){		
				$t = round($bill,2);
				
			}
			else{	
				$price['description'] .= "<br>Расчет:";
			
				$t = round($vol * $bill,2);
				$price['description'] .= "<br>&nbsp;&nbsp; ".($vol)." x ".$bill." = <b>".$t."руб.</b>";
			}
		break;
		case 2:
			$price['description'] .= "<br><i>&nbsp;&nbsp;Расчетная услуга, тариф расчитывается на основе площади дома и площади общего имущества дома.</i>";
			if($Parametrs){
				$t = round(($vol * $sharedsquare) / $allsquare * $bill,2);
				$price['description'] .= "<br>Расчет:<br><i>&nbsp;&nbsp;".($vol)." x ".$sharedsquare." м<sup>2</sup> / ".$allsquare." м<sup>2</sup> x ".$bill." = <b>".$t."руб. за 1 м<sup>2</sup></b></i>";	
			}
			else $t = 0;
		break;
		case 3:
			 $t = 0;
		break;
		case 4:
			$price['description'] .= "<br><i>&nbsp;&nbsp;Расчетная услуга, тариф расчитывается на основе площади дома и площади общего имущества дома.</i>";
			if($Parametrs){	
				if($dep){
					$bvol = $dep['prices']['volume'];
									
					$t = round(($vol * $bvol * $sharedsquare)/$allsquare * $bill,2);
					
					$price['description'] .= "<br>Расчет:<br><i>&nbsp;&nbsp; ".$vol." x ".$bvol." х ".$sharedsquare." м<sup>2</sup> / ".$allsquare." м<sup>2</sup> x ".$bill." = <b>".$t."руб. за 1 м<sup>2</sup></b></i>";	
				}
				else $price['description'] .="<br>Не определена зависимая услуга:".print_r($dep,true);
			}
			else $t = 0;
		break;	
		case 5:
			$price['description'] .= "<br>Расчет:<br>";	
				if($dep){
					$bvol = $dep['prices']['volume'];
									
					$t = round($vol * $bill * $bvol,2);
					
					$price['description'] .= "<i>&nbsp;&nbsp;".$vol." x ".$bill." x ".$bvol." = <b>".$t."руб.</b></i>";	
				}

		break;
	}
	
	$price['calcprice'] = $t;
	if(!isset($price['price']))$price['price'] = 0;

	return $price;
}


?>