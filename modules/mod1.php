<?php
/***********************************************************************************************
* 
* 								MOD1 - УПРАВЛЕНИЕ ПУБЛИКАЦИЯМИ
* 
************************************************************************************************/
//---------------------------------------------------------
if(!defined("pubNews"))					define("pubNews",1);
if(!defined("pubDocuments"))			define("pubDocuments",2);
if(!defined("pubReports"))				define("pubReports",3);
if(!defined("pubOther"))				define("pubOther",4);
if(!defined("pubLinks"))				define("pubLinks",5);

//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : doNope;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 0;
	
	$user = GetCurrentUser();
	$result = Array('result'=>false,'message'=>'','useform'=>false);	
	$dbptr = $GLOBALS['dbptr'];
	
	switch($command){
		case doEdit:	
			$pub = $dbptr->LoadRow("SELECT * FROM livPublications WHERE id=$id");	
		case doAdd:		
			$Form['pubdate'] 		= isset($pub['postdate'])		? $pub['postdate'] 		: date("Y-m-d");
			$Form['text'] 			= isset($pub['post'])			? $pub['post'] 			: "";
			$Form['pubheader'] 		= isset($pub['postheader']) 	? $pub['postheader'] 	: "";
			$Form['pubdescription'] = ucfirst(isset($pub['postdescription'])? $pub['postdescription'] : "");
			$Form['type'] = $type;
	
			$Form['handler'] = "modules/mod1.php";
			$Form['arg'] = "&command=".doSave."&id=".$id.'&type='.$type;
			$result['message'] = corePutForm("EditPublicationForm.php",$Form);
			$result['header'] = ($id ? "Редактирование" : "Добавление")." публикации";
			$result['headericon'] = 'templates/base/images/icon_pub.png';
			$result['user'] = $user['name'];
			$result['useform'] = true;	
			$result['result'] = true;
		break;
		case doSave:
			$vals = json_decode($data,true);
			$header =  htmlspecialchars_decode($vals['pub-header']);
			$post	=  htmlspecialchars_decode($vals['pub-text']);
			$postdscr = isset($vals['pub-description']) ? htmlspecialchars_decode($vals['pub-description']) : "";
			$date 	= $vals['pub-date'];
			$userid = $user['id'];
	
			if($id)	$query = "UPDATE livPublications SET post='$post', postdescription='$postdscr',  postheader='$header',postdate='$date' WHERE id=$id";
			else	$query = "INSERT livPublications (post,postheader,postdescription,postdate,user,type) VALUES('$post','$header','$postdscr','$date',$userid,$type)";
			
			if($dbptr->Query($query)){
				$result['message'] .= "Сохранено успешно<br>";//.$query."<br>".print_r($vals,true);
				$result['reload'] = true;
				$result['result'] = true;
						
			}
			else{
				$result['reload'] = false;
				$result['message'] .= $data;
				$result['message'] .= "Ошибка сохранения<br>".$query;
			}				
		break;
		case doDelete:	
			$query = "DELETE FROM livPublications WHERE id=$id";
			if($dbptr->Query($query)){
				$result['message'] = "";
				$result['reload'] = true;
				$result['result'] = true;		
			}
			else{
				$result['reload'] = false;
				$result['message'] = "Ошибка удаления<br>".$query;
			}					
		break;
	}
	
	
	
	echo json_encode($result);
	
	unset($_REQUEST['ajax'],$_REQUEST['type'],$_REQUEST['data']);	
}

//---------------------------------------------------------
function pubInit()
{
$GLOBALS['itemtypes'] = coreLoadProperty("pubTypes");
$GLOBALS['chapters'] = $GLOBALS['itemtypes'];	
$GLOBALS['chapters'][0] = "Документы";
}

//---------------------------------------------------------
function pubContent()
{
	$pubTypes = $GLOBALS['itemtypes'];
	$type = $GLOBALS['command']['type'];
	$com = $GLOBALS['command']['command'];
	
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	

switch($com){
//Список объектов-----------------------------------------------------------------------------------------------
	case doList:
		tInsertContentHeader(Array('pagetitle'=>"Список: ".$pubTypes[$type]));
		
		$pubs = $dbptr->LoadData("	SELECT pub.id,pub.type,postheader,postdescription, ".($type==pubNews || $type==pubLinks ? "post,": "")." DATE_FORMAT(postdate, '%d.%m.%Y') as postdate,postdate as sortdate ,user.username as user
									FROM livPublications pub
									LEFT JOIN sysUserAuth user on user.id = pub.user
									WHERE type=$type ORDER by sortdate DESC");
		
		
		$out = Array('');
		for($a=0;$a<count($pubs);$a++){
			$pubs[$a]['user'] = GetShortName($pubs[$a]['user']);
			switch($type){
				case pubNews:
					$out = $pubs[$a];			
				break;
				case pubDocuments:
					$out = $pubs[$a];
					$out['postheader'] = "<a href='".makeLink(1,2,0,doShow,pubDocuments,$out['id'])."'>".$out['postheader']."</a>";
					$out['post'] = $out['postdescription'];
					
				break;		
				case pubLinks:
					$out = $pubs[$a];
					$out['postheader'] = "<a href='".$out['postdescription']."' target='_blank'>".$out['postheader']."</a>";	
				break;
			}
			
			$out['handler'] = 'modules/mod1.php';
			tInsertPost($out);
		}
		
		
		tInsertContentFooter();
	break;
//Вывод объекта-----------------------------------------------------------------------------------------------
	case doShow:
		$id = $GLOBALS['command']['id'];
		$out = $dbptr->LoadRow("SELECT id,user,postheader as pheader,post, DATE_FORMAT(postdate, '%d.%m.%Y') as postdate, type FROM livPublications WHERE id=$id");
		
		$out['handler'] = 'modules/mod1.php';	
		
		$out['files'] = 10;
		
		tInsertContentHeader(Array('pagetitle'=>$out['pheader']));			
		tInsertPost($out);		
		tInsertContentFooter();	
	break;
}
}

//---------------------------------------------------------
function pubGetMenu()
{
	$menu = Array(	'footmenu'=>true,
					'image'=>'images/icons/small_clip.png',
					'link'=>'',
					'text'=>'Информация для жителей',
					'submenu'=>Array(	0=>Array(	'image'	=>'images/icons/small_doc.png',
													'link'	=> makeLink(1,2,0,doList,pubDocuments,0),
													'text'	=>'Нормативные документы'
											),
										1=>Array(	'image'	=>'images/icons/small_link.png',
													'link'	=>makeLink(1,3,0,doList,pubLinks,0),
													'text'	=>'Ссылки'
											)			
									)
				);
	return $menu;
	
}
//---------------------------------------------------------
function pubGetLister()
{
	$com = $GLOBALS['command'];
	$command = $com['command'];
	$mod = $GLOBALS['module'];
	$chapter = $GLOBALS['command']['chapter'];
	$user = GetCurrentUser();
	
	
	$list = Array(
			0=>Array(
				doShow=>'',
				doList=>($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod1.php\",\"command=".doAdd."&type=".$com['type']."&ajax=1\")'>Добавить</a></div>" : "")
			)
		);
	

	return  $list[0][$command];	
}

?>