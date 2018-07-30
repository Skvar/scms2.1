<?php
if(!defined("tabWelcome"))				define("tabWelcome",0);
if(!defined("tabCountners"))			define("tabCountners",1);
if(!defined("tabAccount"))				define("tabAccount",2);
if(!defined("tabAccSettings"))			define("tabAccSettings",3);

if(!defined("doSavePassword"))			define("doSavePassword",2);

 

//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : Array();
	
	$user = GetCurrentUser();
	$accountID = $user['account'];
	$date = date("Y-m-d");
	$dateof = date("Y-m-01");
	
	$result = Array('result'=>false,'message'=>'01');
	$msgstring = "";
	
	
	switch($mode){
		case doMarkReads:
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
			if($id){
				if($dbptr->Query("UPDATE livUserMessages SET userreads=1 WHERE id=$id")){
					$result['result'] = true;
					$result['reload'] = true;
				}
			}
			
		break;	
	}
	

	unset($_REQUEST['ajax']);
	unset($_REQUEST['mode']);
	unset($_REQUEST['data']);
	
	echo json_encode($result);
	
}
else{
		
	iInsertPages(Array('Добро пожаловать'=>'','Показания счетчиков'=>'','Карточка лицевого счета'=>'','Управление паролем'=>''),$tab);
	$AccountInfo = $dbptr->LoadRow("SELECT acc.number as accNumber, location,acc.root as buildID,employer,registered,square,aptnumber,street,build.number as housenumber FROM livComAccounts acc JOIN livBuilding build on build.id=acc.root WHERE acc.id=$accountID");
	$tmp = $dbptr->LoadRow("SELECT SUM(bill) as summ FROM livComSaldo WHERE account=$accountID AND service<>0");
	$saldo = $tmp['summ'];
	$tmp = $dbptr->LoadRow("SELECT SUM(bill) as summ FROM livComAccrual WHERE account=$accountID");
	$accrual = $tmp['summ'];
	$tmp = $dbptr->LoadRow("SELECT SUM(bill) as summ FROM livComRecalc WHERE account=$accountID");
	$recalc = $tmp['summ'];
	$tmp = $dbptr->LoadRow("SELECT SUM(bill) as summ FROM livComPay WHERE account=$accountID");
	$pay = $tmp['summ'];
	
	

	switch($tab){	 
		case tabWelcome:
			$user = GetCurrentUser();
			if($user['firstenter']) userFirstEnter($user['id']);
			
			
			$messges = $dbptr->LoadData("SELECT id,header as postheader,message as post,livUserMessages.date as postdate,oncemsg,userreads FROM livUserMessages WHERE user=".$user['id']." OR broadcast=1 ORDER BY livUserMessages.date,livUserMessages.lock DESC");
	
	
//important messages----------------------------------------------------------------------------------------------
			foreach($messges as $ix => $msg){
				$msg['handler'] = 'modules/mod01.php';
				$msg['arg'] = "id=".$msg['id']."&mode=".doMarkReads;
				if($msg['oncemsg'] && !$msg['userreads']) tInsertPost($msg,'user-message',true);
			}
//build link------------------------------------------------------------------------------------------------------
			
			$out['postheader'] = "Информация о лицевом счете №".$AccountInfo['accNumber'];
			$out['post'] = "Адрес: <a href='".makeLink(2,1,0,doShow,0,$AccountInfo['buildID'])."'>".$AccountInfo['street']." д.".$AccountInfo['housenumber']."</a> кв.".$AccountInfo['aptnumber']."<br>";
			$out['post'].= "Площадь помещений: ".$AccountInfo['square']."м<sup>2</sup>  ;Прописано: ".$AccountInfo['registered']." чел.<br>";
			$out['post'].= "Собственник: ".$AccountInfo['employer'];
			
			tInsertPost($out,'user-message',false);
//Histogramma-----------------------------------------------------------------------------------------------------			
			$query = "SELECT billdate as ddate,TRUNCATE(SUM(bill), 2) as bill FROM livComSaldo WHERE account=$accountID AND service=0 GROUP BY billdate ORDER BY billdate DESC";
			$tmp = $dbptr->LoadData($query,false);
			
			$bills = Array();
			if(is_array($tmp)){
				
				foreach($tmp as $key => $val){
					$tmp[$key]['ddate'] = $val['ddate'];
					$key = strftime("%B %Y",strtotime($val['ddate']));
					$bills[$key] = $val['bill'];
				}
				unset($tmp);
		
						
				$Histogramm = Array('width'=>750,'height'=>120,'data'=>Array('Задолженость'=>$bills));
				$his = corePutForm("histogramm.php",$Histogramm);
				unset($bills);
			}
			echo "<hr>";
			
	
			if($saldo > coreSettings('clientMaxDebt'))	$out['postheader'] = "Согласно ст.153-158 ЖК РФ, Вам необходимо оплатить долг.";
			else				$out['postheader'] = "Спасибо за своевременно оплаченые жилищно-коммунальные услуги";
			$out['post'] = "К оплате за текущий период: ".number_format($saldo + $accrual+$recalc-$pay, 2, ',', ' ')." руб.";
			$out['post'] .="<hr><p align='center'>Динамика задолжености (руб.)";
			
			$out['post'] .= $his;
			$out['post'] .= "</p>";
			
				
			tInsertPost($out,'user-message',false);
			
			
//Messages-------------------------------------------------------------------------------------------------------------	
		
			
			foreach($messges as $ix => $msg){
	
				if(!$msg['oncemsg']) tInsertPost($msg,'user-message',false);
			}
			
			unset($messges);
		break;
		case tabCountners:
			include("mod011.php");
		break;
		case tabAccount:	
			include("mod012.php");
		break;
		case tabAccSettings:
			include("mod013.php");
		break;
	}
}


//----------------------------------------------------------------------------------------------------
function userFirstEnter($userid)
{
	$date = date("Y-m-d");	
	$query = "INSERT livUserMessages (livUserMessages.user,header,message,livUserMessages.date,livUserMessages.lock,broadcast,oncemsg,userreads) 
			  VALUES($userid,'Внимание!','Смените пароль установленный по умолчанию, на другой.','$date',1,0,1,0)";
			  
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	$dbptr->Query($query);
			  	
}




?>