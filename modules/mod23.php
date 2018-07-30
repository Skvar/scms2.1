<?php
/***********************************************************************************************
* 
* 								MOD23 - УПРАВЛЕНИЕ ОРГАНИЗАЦИЯМИ
* 
************************************************************************************************/
if(!defined("orgControl"))					define("orgControl",1);
if(!defined("orgService"))					define("orgService",2);
if(!defined("orgProvider"))					define("orgProvider",3);
if(!defined("orgGoverment"))				define("orgGoverment",4);
if(!defined("orgOther"))					define("orgOther",5);

if(!defined("tabCommon"))					define("tabCommon",0);
if(!defined("tabLicense"))					define("tabLicense",1);
if(!defined("tabDocuments"))				define("tabDocuments",2);

//Подготовка данных---------------------------------------------------------------------------------------------------
//Входные параметры для включения формы в другие разделы--------------------------------------------------
if(!isset($Hidden)) $Hidden = false;
if(!$Hidden){
	$pt="";
	switch($com){
		case doList:
			$out = Array();
			$pt = 'Список контрагентов';
			$GLOBALS['chaptername'] = $pt;	
			$tmp = loadOrganizations(0);
			for($a=0;$a<count($tmp);$a++){
																				
				if($tmp[$a]['id'] == confOurOrg) 	$out[$a]['postheader'] = "<a href='".makeLink(2,chapterOrganization,0,doShow,0,$tmp[$a]['id'])."'><img src='favicon.png' style='margin-right:5px;'>".$tmp[$a]['name']."</a>";
				else 						$out[$a]['postheader'] = "<a href='".makeLink(2,chapterOrganization,0,doShow,0,$tmp[$a]['id'])."'>".$tmp[$a]['name']."</a>";
				$out[$a]['id'] = $tmp[$a]['id'];
				$out[$a]['post'] = 	(isset($tmp[$a]['property']['Директор']) ? ("Директор: ".$tmp[$a]['property']['Директор'])."<br>" : "").
									(isset($tmp[$a]['property']['Адрес']) ? ("Адрес: ".$tmp[$a]['property']['Адрес'])."<br>" : "").
									(isset($tmp[$a]['property']['Телефон']) ? ("Телефон: ".$tmp[$a]['property']['Телефон']."<br>") : "").
									(isset($tmp[$a]['property']['Часы работы']) ? ("Часы работы: ".$tmp[$a]['property']['Часы работы']) : "");
				$out[$a]['handler'] = "modules/mod231.php";	
			}	
			
			for($a=0;$a<count($out);$a++){
			 tInsertPost($out[$a],'orgblock',true);
			}
		break;
		case doShow:
			$tmp = loadOrganizations($id);
			$out['type'] = $tmp['type'];
			tInsertContentHeader(Array('pagetitle'=>$orgTypes[$tmp['type']]." ".$tmp['name']));
			if($out['type'] == orgControl)	iInsertPages(Array('Общая информация'=>'','Лицензия'=>'','Документы'=>''),$tab);
			else							iInsertPages(Array('Общая информация'=>''),$tab);
			switch($tab){		
				case tabCommon:	
					include("mod231.php");	
				break;
				case tabLicense:
					include("mod232.php");	
				break;
				case tabDocuments:
					include("mod233.php");
				break;
			}	
		break;
	}
}

/**************************************************************************************************************

												ФУНКЦИИ

***************************************************************************************************************/

?>