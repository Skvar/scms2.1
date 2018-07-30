<?php
//Вывод данных--------------------------------------------------------------------------------------------------------
//Входные параметры для включения формы в другие разделы--------------------------------------------------
if(!isset($Hidden)) $Hidden = false;
if(!$Hidden){

$wallTypes = coreLoadProperty("wallTypes");
if(!defined("tabCommon"))					define("tabCommon",0);
if(!defined("tabServices"))					define("tabServices",1);
if(!defined("tabBills"))					define("tabBills",2);
if(!defined("tabWorks"))					define("tabWorks",3);
if(!defined("tabActions"))					define("tabActions",4);
if(!defined("tabDocs"))						define("tabDocs",5);
//Подготовка данных---------------------------------------------------------------------------------------------------
	switch($com){
		case doList:
			$opstr = intval($GLOBALS['command']['add']);
			$street = buildGetList(0,btOurControl);
			tInsertContentHeader(Array('pagetitle'=>"Жилой фонд находящийся в управлении ".$GLOBALS['organization']['name']));
			for($a=0;$a<count($street);$a++){
				if(count($street[$a]['out'])>0){
					$out['icon'] = './templates/base/images/icon_street.png';
					$out['header'] = $street[$a]['street'].":".$street[$a]['addstreetinfo'];
					$out['anchor'] = 'anchor'.$street[$a]['sid'];
					for($b=0;$b<count($street[$a]['out']);$b++){
						$out['out'][$b]['background'] = './templates/base/images/logo_street.png';
						$out['out'][$b]['header'] = "<a href='".makeLink(2,chapterBuilding,0,doShow,0,$street[$a]['out'][$b]['id'])."'>".$street[$a]['out'][$b]['name']."</a>";
						$bldYear = 	isset($street[$a]['out'][$b]['property']['Год постройки']) ? $street[$a]['out'][$b]['property']['Год постройки'] : "Не указано";
						$wllMat = 	isset($street[$a]['out'][$b]['property']['Материал стен']) ? $wallTypes[$street[$a]['out'][$b]['property']['Материал стен']] : "Не указано";
						$numApt = 	isset($street[$a]['out'][$b]['property']['Квартиры']) ? $street[$a]['out'][$b]['property']['Квартиры'] : "Не указано";
						if(isset($street[$a]['out'][$b]['property']['Обслуживающая организация'])){
							$org = loadOrganizations($street[$a]['out'][$b]['property']['Обслуживающая организация']);
							$orgname = isset($org['name']) ? $org['name'] : "";
							$orgloc = isset($org['property']['Адрес']) ? $org['property']['Адрес'] :"";
							$orgtel = isset($org['property']['Телефон']) ? $org['property']['Телефон'] : "";
							unset($org);
						} 
						else{
							$orgname = "";
							$orgloc = "";
							$orgtel = "";					
						} 			
						$out['out'][$b]['text'] = "<pre>Год постройки: ".$bldYear." г.\nМатериал стен: ".$wllMat."\nКоличество квартир: ".$numApt."\nОбслуживающая организация: ".$orgname."\n   Адрес: ".$orgloc."\n   Телефоны: ".$orgtel."</pre>";
					}
				
				if($street[$a]['sid'] == $opstr) tInsertHierarchyBlock($out,'buildings',true);
				else							tInsertHierarchyBlock($out,'buildings',false);
				
				unset($out);
				}
			}
			
			if($opstr>0) echo "<script>$(document).on('load', scrollToAnchor('anchor$opstr'));</script>";
			unset($street);	
			
			tInsertContentHeader(Array('pagetitle'=>"Жилой фонд вышедший из под управления ".$GLOBALS['organization']['name']));
			$street = buildGetList(0,btOutOfControl);
			
			for($a=0;$a<count($street);$a++){
				if(count($street[$a]['out'])){
					$out['icon'] = './templates/base/images/icon_street.png';
					$out['header'] = $street[$a]['street'].":".$street[$a]['addstreetinfo'];
					$out['anchor'] = 'anchor'.$street[$a]['sid'];
					for($b=0;$b<count($street[$a]['out']);$b++){
						$out['out'][$b]['background'] = './templates/base/images/logo_street.png';
						$out['out'][$b]['header'] = "<a href='".makeLink(2,chapterBuilding,0,doShow,0,$street[$a]['out'][$b]['id'])."'>".$street[$a]['out'][$b]['name']."</a>";
						$bldYear = 	isset($street[$a]['out'][$b]['property']['Год постройки']) ? $street[$a]['out'][$b]['property']['Год постройки'] : "Не указано";
						$wllMat = 	isset($street[$a]['out'][$b]['property']['Материал стен']) ? $wallTypes[$street[$a]['out'][$b]['property']['Материал стен']] : "Не указано";
						$numApt = 	isset($street[$a]['out'][$b]['property']['Квартиры']) ? $street[$a]['out'][$b]['property']['Квартиры'] : "Не указано"; 			
						$out['out'][$b]['text'] = "<pre>Год постройки: ".$bldYear." г.\nМатериал стен: ".$wllMat."\nКоличество квартир: ".$numApt."\n</pre>";
					}
					if($street[$a]['sid'] == $opstr) tInsertHierarchyBlock($out,'buildings',true);
					else							tInsertHierarchyBlock($out,'buildings',false);
					
					unset($out);
				}
	
			}
				
		break;
		case doShow:
			$build = buildGetList($id);	
			if($build['id']){
				tInsertContentHeader(Array('pagetitle'=>$build['name']));
				iInsertPages(Array('Общая информация'=>'','Услуги'=>'','Средства'=>true,'Текущий ремонт'=>'','Плановые мероприятия'=>true,'Документы'=>''),$tab);
				switch($tab){
					case tabCommon:
						include("mod210.php");
					break;
					case tabServices:
						include("mod211.php");
					break;
					case tabBills:
						include("mod212.php");
					break;
					case tabWorks:
						include("mod213.php");
					break;
					case tabActions:
						include("mod214.php");
					break;
					case tabDocs:
						include("mod215.php");
					break;
				}
			}			
		break;
	}
	//Вывод данных--------------------------------------------------------------------------------------------------------

	tInsertContentFooter();
	
	unset($Hidden);
}





?>