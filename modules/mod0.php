<?php
//---------------------------------------------------------



//---------------------------------------------------------
function admInit()
{
$GLOBALS['itemtypes'] = coreLoadProperty("orgTypes");	
$com = $GLOBALS['command']['command'];
if($com==doLogOut) LogOut();



$GLOBALS['countstats'] = Array('enabled'=>$GLOBALS['settings']['count_enabled'],'arc_loaded'=>$GLOBALS['settings']['count_saved'],'arc_cleared'=>$GLOBALS['settings']['count_cleared']);

	
}

//---------------------------------------------------------
function admContent()
{
	$pubTypes = $GLOBALS['itemtypes'];
	$type = $GLOBALS['command']['type'];
	$com = $GLOBALS['command']['command'];
	$tab = $GLOBALS['command']['tab'];
	
	
	
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$usr = GetCurrentUser();
	
	if($usr===false) return;
	
	
		

	if($usr['right']==0){
		tInsertContentHeader(Array('pagetitle'=>'Вход в личный кабинет'));
		tAuthPanel(Array(	'header'=>'Авторизация пользователя',
							'user-name'=>'Логин',
							'user-name-plc'=>'Фамилия',
							'user-pwd'=>'Пароль',
							'user-pwd-plc'=>'Введите пароль'
						)
					);
		echo "<hr>";
		echo "<p class='small-text' align='center'>Для входа используйте фамилию нанимателя, как логин и пароль (по умолчнию - номер вашего лицевого счета).</p>";	
		echo "<p class='small-text' align='center'>В случае появления затруднений при входе в личный кабинет, звоните 8(39168)4-30-26.</p>";	
		echo "<br>";
	}
	else{
		tInsertContentHeader(Array('pagetitle'=>$usr['name']));
		$accountID = $usr['account'];
		
		
		if($usr['right']<100) 		include("mod01.php");//Обычные клиенты
		else if($usr['right']>=100)	include("mod02.php");//Суперюзеры 
		
	}
	
	
	tInsertContentFooter();
}
//---------------------------------------------------------
function admGetMenu()
{
	$usr = GetCurrentUser();
	
	if($usr['right']>0){
			$menu = Array(	
							'image'=>'images/icons/small_pa.png',
							'link'=>'',
							'text'=>'Личный кабинет',
							'submenu'=>Array(	0=>Array(		'image'	=>	'images/icons/small_pa1.png',
																'link'	=>	makeLink(99,0,0,0,0,0),
																'text'	=>	'Личный кабинет'
														),
												1=>Array(		'image'	=>	'images/icons/small_exit.png',
																'link'	=>	makeLink(99,0,0,doLogOut,0,0),
																'text'	=>	GetShortName($usr['name']).' : Выход'
														)
											)
			);
	}
	else{
		$menu = Array(	'image'=>'images/icons/small_key.png',
						'link'=>makeLink(99,0,0,0,0,0),
						'text'=>'Вход в личный кабинет',
					);
	}
	

	return $menu;
}
//---------------------------------------------------------
function admGetLister()
{
	$com = $GLOBALS['command'];
	$command = $com['command'];
	$tab = $com['tab'];
	$mod = $GLOBALS['module'];
	$id = $com['id'];
	$chapter = $GLOBALS['command']['chapter'];
	$user = GetCurrentUser();
	
	$build = Array('root'=>0,'street'=>'','buildclosed'=>0);
	
	if($chapter == chapterBuilding && $command==doShow) $build = buildGetList($id);
	
	$list = Array(
		0=>Array(
			'4'=>($user['right']>=100 ? "<div class='page-lister-element'><a onclick='Commandsd(\"modules/mod024.php\",\"".coreChangeURI($com['uri'],'command',doAdd)."&ajax=1\")'>Добавить</a></div>" :""),	
			'all'=>''
		)
	);
	

	return $list[$chapter]['all'].(isset($list[$chapter][$tab]) ? $list[$chapter][$tab] : "");
}

?>