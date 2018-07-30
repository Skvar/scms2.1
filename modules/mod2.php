<?php
//---------------------------------------------------------
if(!defined("chapterBuilding"))				define("chapterBuilding",1);
if(!defined("chapterPrices"))				define("chapterPrices",2);
if(!defined("chapterOrganization"))			define("chapterOrganization",0);

if(!defined("btOurControl"))				define("btOurControl",1);
if(!defined("btOutOfControl"))				define("btOutOfControl",2);
if(!defined("btAll"))						define("btAll",0);


//---------------------------------------------------------
function orgInit()
{
$GLOBALS['itemtypes'] = coreLoadProperty("orgTypes");	
$GLOBALS['chapters'] = coreLoadProperty("orgChapters");
}

//---------------------------------------------------------
function orgContent()
{
	$orgTypes = $GLOBALS['itemtypes'];
	$id = $GLOBALS['command']['id'];
	$type = $GLOBALS['command']['type'];
	$com = $GLOBALS['command']['command'];
	$tab = $GLOBALS['command']['tab'];
	$chapter = $GLOBALS['command']['chapter'];
	
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	
	switch($chapter){
		case chapterBuilding:
			include("mod21.php");
		break;
		case chapterPrices:
			include("mod22.php");
		break;
		case chapterOrganization:
			include("mod23.php");
		break;
	}
	
	

	
	
	
}

//---------------------------------------------------------
function orgGetMenu()
{
	$menu = Array(	'footmenu'=>true,
					'image'=>'images/icons/logo_small.png',
					'link'=>'',
					'text'=>$GLOBALS['organization']['name'],
					'submenu'=>Array(	0=>Array(	'image'	=>'images/icons/home.png',
													'link'	=>makeLink(2,chapterBuilding,0,doList,0,0),
													'text'	=>'Жилой фонд'
											),
										1=>Array(	'image'	=>'images/icons/small_basket.png',
													'link'	=>makeLink(2,chapterPrices,0,doList,0,0),
													'text'	=>'Тарифы'
											),
										2=>Array(	'image'	=>'images/icons/logo_small.png',
													'link'	=>makeLink(2,chapterOrganization,0,doShow,0,confOurOrg),
													'text'	=>$GLOBALS['organization']['name']
											),
										3=>Array(	'image'	=>'images/icons/person.png',
													'link'	=>makeLink(2,chapterOrganization,0,doList,0,0),
													'text'	=>'Список контрагентов'
											)			
									)
				);
	

	return $menu;	
}
//---------------------------------------------------------
function orgGetLister()
{
	$com = $GLOBALS['command'];
	$command = $com['command'];
	$tab = $com['tab'];
	$mod = $GLOBALS['module'];
	$id = $com['id'];
	$chapter = $GLOBALS['command']['chapter'];
	$user = GetCurrentUser();
	
	$build = Array('root'=>0,'street'=>'','buildclosed'=>0);
	$org = Array('license'=>Array('id'=>0));
	
	if($chapter == chapterBuilding && $command==doShow) $build = buildGetList($id);
	if($chapter == chapterOrganization && $command==doShow) $org = loadOrganizations($id);
	
	$list = Array(
		chapterBuilding=>Array(
			doShow=>Array(
				'all'=>"<div class='page-lister-element'><a href='".makeLink(2,chapterBuilding,0,doList,0,0,"add=".$build['root'])."'>ул.".$build['street']."</a></div>",
				'0'=>($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod210.php\",\"".coreChangeURI($com['uri'],'command',doEdit)."&ajax=1\")'>Редактировать дом</a></div>" :"").
					 ($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod210.php\",\"".coreChangeURI($com['uri'],'command',doEdit)."&dog=1&ajax=1\")'>Редактировать договор</a></div>" :"").
					 ($user['right']>=100 && !$build['buildclosed'] ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod210.php\",\"".coreChangeURI($com['uri'],'command',doEdit)."&dog=1&close=1&ajax=1\")'>Закрыть договор</a></div>" :""),	
				'3'=>($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod213.php\",\"".coreChangeURI($com['uri'],'command',doAdd)."&ajax=1\")'>Добавить</a></div>" :"")	
			),
			doList=>Array(
				'all'=>''
			)
		),
		chapterPrices=>Array(
			doShow=>Array(
				'all'=>($user['right']>=100 ?
						"<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod22.php\",\"command=".doAdd."&ajax=1&rootid=$id\")'>Добавить услугу</a></div>" : "")
	
			),
			doList=>Array(
				'all'=>($user['right']>=100 ?
						"<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod22.php\",\"".coreChangeURI($com['uri'],'command',doAdd)."&ajax=1\")'>Добавить услугу</a></div>" : "")
			)
		),
		chapterOrganization=>Array(
			doShow=>Array(
				'all'=>"",
				'0'=>($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod231.php\",\"command=".doEdit."&ajax=1&id=$id\")'>Править</a></div>":""),
				'1'=>($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod232.php\",\"command=".doEdit."&ajax=1&id=$id&licid=".(isset($org['license']['id'])? $org['license']['id'] : 0)."\")'>Править</a></div>":"")
		
			),
			doList=>Array(
				'all'=>($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod231.php\",\"".coreChangeURI($com['uri'],'command',doAdd)."ajax=1\")'>Добавить</a></div>":"")
			)
		)	
	);
	

	return $list[$chapter][$command]['all'].(isset($list[$chapter][$command][$tab]) ? $list[$chapter][$command][$tab] : "");	
}


//---------------------------------------------------------
function loadOrganizations($id)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	$user = GetCurrentUser();
	
	if($id){
		$out = $dbptr->LoadRow("SELECT * FROM livOrganizations WHERE id=$id");
		$out['property'] =  $dbptr->LoadData("SELECT description,property FROM livObjProperty WHERE type = 0 AND owner=$id",true,'description','property');
		$out['license'] =  $dbptr->LoadRow("SELECT DATE_FORMAT(begdate, '%d.%m.%Y') as begdate, DATE_FORMAT(enddate, '%d.%m.%Y') as enddate,number,owner,description,livLicense.order,id FROM livLicense WHERE owner=$id AND CURDATE()>=begdate AND CURDATE()<=enddate");		
		if($out['license']===false) $out['license'] = Array('number'=>0,'owner'=>$id,'begdate'=>'','enddate'=>'','description'=>'Нет информации о лицензии','order'=>'');

		
		
	}
	else{
		if($user['right']>=100)	$out = $dbptr->LoadData("SELECT * FROM livOrganizations");
		else					$out = $dbptr->LoadData("SELECT * FROM livOrganizations WHERE type IN(1,2,3) AND hidden=0");
		for($a=0;$a<count($out);$a++){
			$out[$a]['property'] =  $dbptr->LoadData("SELECT description,property FROM livObjProperty WHERE type = 0 AND owner=".$out[$a]['id'],true,'description','property');			
		}
	}
	
	
	return $out;	
}

/**************************************************************************************************************

												ФУНКЦИИ

***************************************************************************************************************/
function buildGetList($id = 0,$ctrlType = btAll)
{
	$mf = Array('phones','fax','worktime');
	$prop = Array();
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	if($id==0){
		$query = "SELECT id as sid,street,postfix FROM livBuilding WHERE type=1 ORDER BY street";	
		$out = $dbptr->LoadData($query);
			
			
			for($a=0;$a<count($out);$a++){
				switch($ctrlType){
					case btAll:
						 $query = "SELECT hs.id,str.postfix,str.street,hs.name FROM livBuilding hs JOIN livBuilding str on hs.root = str.id  WHERE hs.type=2 AND hs.root=".$out[$a]['sid']." ORDER BY hs.street,hs.number";
					break;
					case btOurControl:
						$query = "SELECT hs.id,str.postfix,str.street,hs.name FROM livBuilding hs JOIN livBuilding str on hs.root = str.id  JOIN livBuildLicense lic on lic.build=hs.id AND NOW() <= lic.enddate  WHERE hs.type=2 AND hs.root=".$out[$a]['sid']." ORDER BY hs.street,hs.number";				
					break;
					case btOutOfControl:
						$query = "SELECT hs.id,str.postfix,str.street,hs.name FROM livBuilding hs JOIN livBuilding str on hs.root = str.id  JOIN livBuildLicense lic on lic.build=hs.id AND NOW() > lic.enddate  WHERE hs.type=2 AND hs.root=".$out[$a]['sid']." ORDER BY hs.street,hs.number";
				
					break;
					
				}
				$out[$a]['out'] = $dbptr->LoadData($query);
				

				
				
				$out[$a]['street'] = $out[$a]['postfix']." ".$out[$a]['street'];
				$out[$a]['addstreetinfo'] = "Количество домов на улице: ".count($out[$a]['out']);
				for($b=0;$b<count($out[$a]['out']);$b++){
					$out[$a]['out'][$b]['property'] = loadBuildProperty($out[$a]['out'][$b]['id'],"'Год постройки','Материал стен','Квартиры','Обслуживающая организация'");			
				}
				
				unset($tmp);
			}
	}
	else{
		$query = "SELECT bld.*,lic.dognum,DATE_FORMAT(lic.begdate, '%d.%m.%Y') as begdate,DATE_FORMAT(lic.enddate, '%d.%m.%Y') as enddate,lic.closedescription,(lic.enddate<NOW()) as buildclosed FROM livBuilding bld LEFT JOIN livBuildLicense lic on lic.build = bld.id  WHERE bld.id=$id";
		$out = $dbptr->LoadRow($query);	
		if(is_array($out)){
				$out['property'] = loadBuildProperty($id);
		}
		else{
							$out['id']  = 0;
							$out['root'] = 0;
							$out['street'] = "";
		}
							
	}
		
	return $out;
}

//--------------------------------------------------------------------------------------------------------------
function loadBuildProperty($id,$list="")
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$add = "";
	if($list!="") $add = "AND description IN($list)";
	$query = "SELECT description,property FROM livObjProperty WHERE type=1 AND owner=$id ".$add;
	
	$out = $dbptr->LoadData($query,true,'description','property');
	
	if(!isset($out["Площадь нежилых помещений"])) $out["Площадь нежилых помещений"] = 0;
	
	
	return $out;
}

?>