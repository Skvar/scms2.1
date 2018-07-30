<?php

function coreInit()
{

	setlocale(LC_ALL, 'ru_RU.UTF8');
	
	$dbptr = $GLOBALS['dbptr'];
	
	if(!class_exists('CMYSQL')) return false;
	
	if(!CheckAuth()) return false;
	
	
		
	$GLOBALS['organization'] = $dbptr->LoadData("SELECT * FROM livObjProperty WHERE type = 0 AND owner = ".confOurOrg,true,'name','property');	
	
	$tmp = $dbptr->LoadRow("
		SELECT org.name,org.description,prop.value as orgType,lic.id as licId
		FROM livOrganizations org 
		LEFT JOIN livProperty prop on prop.section='orgTypes' AND org.type=prop.index 
		LEFT JOIN livLicense lic on lic.owner = org.id
		WHERE org.id=".confOurOrg);
	$GLOBALS['organization']['id'] = confOurOrg;	
	$GLOBALS['organization']['type'] = $tmp['orgType'];
	$GLOBALS['organization']['name'] = $tmp['name'];
	$GLOBALS['license']['id'] = isset($tmp['licId']) ? $tmp['licId'] : 0 ;
						
	$GLOBALS['settings'] = $dbptr->LoadData("SELECT * FROM sysSettings",true,'name','value');	
//check modules----------------------------------------------------------------
	$GLOBALS['menu'][0]['link'] = 'index.php';
	$GLOBALS['menu'][0]['image'] = 'images/icons/home.png';
	$GLOBALS['menu'][0]['text'] = 'Главная';
//-----------------------------------------------------------------------------

	$mods = $dbptr->LoadData("SELECT id FROM sysModules ORDER BY position");	
	for($a=0;$a<count($mods);$a++){
		$mod = coreGetLoadModule($mods[$a]['id']);
		if($mod['result'] == true){
			$GLOBALS['module'] = $mod;
			include($mod['codebody']);
			$smen =  coreSafeFunction('GetMenu');
			$GLOBALS['menu'][] =$smen;
		}
		unset($mod);
	}
	unset($mod);
	
	
	
	
//Load current page-------------------------------------------------------------
	$GLOBALS['command'] = coreParseURI(); 
	$mod =  coreGetLoadModule($GLOBALS['command']['mod']);
	if($mod['result'] == false) trigger_error($mod['resultmessage'],E_USER_ERROR);
	else{
		$GLOBALS['module'] = $mod;
		coreSafeFunction('Init');
		return true;
	}
	
	return false;	
}
//Авторизация и проверка юзера---------------------------------------------------
function CheckAuth()
{
$dbptr = $GLOBALS['dbptr'];
if(!class_exists('CMYSQL')) return false;

if(isset($_SESSION['user'])) unset($_SESSION['user']) ;

$userip = $_SERVER["REMOTE_ADDR"];

$sst = session_status();
if($sst != PHP_SESSION_NONE){
	if (isset($_COOKIE['uid']) and isset($_COOKIE['uhash'])){
		$id = intval($_COOKIE['uid']);
		$hash = $_COOKIE['uhash'];
		$agree = isset($_COOKIE['useragree']) ? $_COOKIE['useragree'] : 0;
		$uinfo = $dbptr->LoadRow("SELECT * FROM sysUserAuth WHERE id=".$id);
		if($uinfo !== false && $uinfo['hash'] === $hash && $uinfo['id'] == $id){
			session_destroy();//удаляем старую
			session_start();//запускаем новую
			$_SESSION['user'] = Array('user-agree'=>$agree,'id'=>$uinfo['id'],'right'=>$uinfo['userright'],'name'=> $uinfo['username'],'account'=> $uinfo['account'],'firstenter'=>($uinfo['lastip']=="" ? true : false));
			$dbptr->Query("UPDATE sysUserAuth SET lastlogin='".date("Y-m-d")."',lastip='$userip' WHERE id=$id");
			
			coreSaveStats($id,$userip);
			return true;		
		}
		else{
			$res = 0;
			$res += setcookie("uid", "");
	        $res += setcookie("uhash", "");	    
		}
	}

	if(!isset($_SESSION['user'])){  
		if(isset($_REQUEST['user-name']) && isset($_REQUEST['user-pwd']) && isset($_REQUEST['user-agree'])){
			$username =  htmlspecialchars($_REQUEST['user-name'],ENT_QUOTES);
			$userpass =  htmlspecialchars($_REQUEST['user-pwd'],ENT_QUOTES);
			$useragree =  $_REQUEST['user-agree']=='on'?true:false;
			
				
			$users = $dbptr->LoadData("SELECT * FROM sysUserAuth WHERE login='$username'");
					
			if(is_array($users)){
				for($s=0;$s<count($users);$s++){		
					if($users[$s]['pwd'] === (md5($userpass))){
						$id = $users[$s]['id'];
						
						

				        $hash = md5(sysAuthHashCode(10));

				        if(!$dbptr->Query("UPDATE sysUserAuth SET hash='".$hash."',lastlogin='".date("Y-m-d")."',lastip='$userip', agree=$useragree WHERE id=$id")){
							
						}

						
				        setcookie("uid", $users[$s]['id'], time()+cookieTime);
				        setcookie("uhash", $hash, time()+cookieTime);
				        
				        $uinfo = $users[$s];
				        session_destroy();//удаляем старую
						session_start();//запускаем новую
						$_SESSION['user'] = Array('user-agree'=>$useragree,'id'=>intval($uinfo['id']),'right'=>intval($uinfo['userright']),'name'=> $uinfo['username'],'account'=> $uinfo['account'],'firstenter'=>($uinfo['lastip']=="" ? true : false));
						coreSaveStats(intval($uinfo['id']),$userip); 
						
	
						return true;	    
			    	}
		 		}
		 	}			
		}	
	}
}
coreSaveStats(0,$userip);  
return true;	
}
//--------------------------------------------------------------------------------
function coreSaveStats($userid,$ip)
{
	
$dbptr = $GLOBALS['dbptr'];
if(!class_exists('CMYSQL')) return false;

$request = $_SERVER['REQUEST_URI'];
$agent = $_SERVER['HTTP_USER_AGENT'];
$query = "INSERT INTO livComStats (ip,request,user,agent) VALUES('$ip','$request',$userid,'$agent')";

$dbptr->Query($query);
	
}
//--------------------------------------------------------------------------------
function LogOut()
{

	$sst = session_status();
	if($sst != PHP_SESSION_NONE){
		$user = GetCurrentUser();
		unset($_SESSION['user']);
		
		session_destroy();
		if(!headers_sent($filename, $linenum)){
			setcookie("uid", "", time() - cookieTime);
    		setcookie("uhash", "", time() - cookieTime);
    	}
    	echo "<script>location.href = 'index.php';</script>";
	}

}
//--------------------------------------------------------------------------------
function GetCurrentUser()
{
		if(isset($_SESSION['user']))	return $_SESSION['user'];
		else return  Array('id'=>0,'right'=>0,'name'=> "Анонимный пользователь",'firstenter'=>true);	
}

//--------------------------------------------------------------------------------
function sysAuthHashCode($length=6) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;  
    while (strlen($code) < $length) $code .= $chars[mt_rand(0,$clen)];  
    return $code;

}
//--------------------------------------------------------------------------------
function makeLink($mod,$chapter,$tab,$command,$type,$id,$add="")
{
	if(isset($GLOBALS['command'])) parse_str($GLOBALS['command']['uri'],$tags);
	
	$link = 'index.php?';
	$link .= 'mod='.(intval($mod) >= 0 ? $mod : $tags['mod']);
	$link .= '&chapter='.(intval($chapter)  >= 0 ? $chapter : $tags['chapter']);
	$link .= '&tab='.(intval($tab) >= 0 ? $tab : $tags['tab']);
	$link .= '&command='.(intval($command) >= 0 ? $command : $tags['command']);
	$link .= '&type='.(intval($type) >= 0 ? $type : $tags['type']);
	$link .= '&id='.(intval($id)  >= 0 ? $id : $tags['id']);
	$link .= '&'.($add != "" ? $add : (isset($tags['add'])?$tags['add']:""));
	return $link;
	
}
//------------------------------------------------------------------------------------------
function coreHideInclude($path)
{
	$Hidden=true;
	include($path);
	unset($Hidden);
}
//------------------------------------------------------------------------------------------
function corePutForm($formname,$Form)
{
	$Histogramm = $Form;//Костыль для гистограмм
	ob_start();
	include($formname);
	$out = ob_get_contents();
	ob_clean();
	return $out;
}
//-------------------------------------------------------------------------------------------	
function coreParseURI()
{
	global $Settings;
	$Command = Array();
		 $Command['uri']	= $_SERVER['QUERY_STRING'];
		 $Command['id'] 	= isset($_GET['id']) 		? intval($_GET['id']) 		: 0;
		 $Command['type']	= isset($_GET['type']) 		? intval($_GET['type']) 	: 1;
		 $Command['tab']	= isset($_GET['tab']) 		? intval($_GET['tab']) 		: 0;
		 $Command['mod']	= isset($_GET['mod']) 		? intval($_GET['mod']) 		: (isset($Settings['defaultmodule']) ? $Settings['defaultmodule'] : 1);
		 $Command['chapter']= isset($_GET['chapter']) 	? intval($_GET['chapter'])	: 0;
		 $Command['command']= isset($_GET['command']) 	? intval($_GET['command']) 	: doList; 
		 $Command['add']    = isset($_GET['add']) 	? ($_GET['add']) 	: ""; 
		 
		 return $Command;

}
//-------------------------------------------------------------------------------------------
function coreGetLoadModule($modid)
{
	
		$out = Array('result'=>false,'resultmessage'=>"");
		if($modid == 0){
			$out['resultmessage'] = "Неверный идентификатор модуля";
		}
		else{		
				$m = $GLOBALS['dbptr']->LoadRow("SELECT * FROM sysModules WHERE id=$modid");
				
				
					
				if(is_array($m)){
					$moddir = $GLOBALS['settings']['modsdir']."/";
					

							
					 if(file_exists($moddir.$m['body'])){
					 	//------------------------------------	
					 	//------------------------------------
					 	$out['result']			= true;
					 	$out['resultmessage']	.= "true";
						$out['codebody'] 		= strlen($m['body']) ? $moddir.$m['body'] : "";
						$out['codejscript'] 	= strlen($m['jscript']) ? $moddir.$m['jscript'] : "";
						$out['codedescription']	= $m['description'];
						$out['prefix']			= $m['prefix'];
						$out['id'] = $modid;							
					}
					else $out['resultmessage'] .= "File: ".$moddir.$m['body']."  not found<br>";
				}
				else $out['resultmessage'] .= "Module not defined<br>";
		}
			
		
		return $out;
}
//-------------------------------------------------------------------------------------------
function coreChangeURI($uri,$tag,$newval=0,$del=false)
{
	parse_str($uri,$tags);
	
	if(isset($tags[$tag])){
		$t = $tag."=".$tags[$tag];
		
		if($del)	$t1 = "";
		else		$t1 = $tag."=".$newval;
		
		$uri = str_replace($t,$t1,$uri);
	}
	else{
		$uri.='&'.$tag."=".$newval;
		
	}
	
	if(isset($tags['add'])) $uri = str_replace("add=".$tags['add'],"",$uri);
	
	return $uri;
	
	
}

//-------------------------------------------------------------------------------------------
function coreSettings($key)
{
	$res = isset($GLOBALS['settings'][$key]) ? $GLOBALS['settings'][$key]: "0";
	
	return $res;
}
//-------------------------------------------------------------------------------------------
function coreSafeFunction($func)
{
	$out = Array();
	if(isset($GLOBALS['module']['prefix'])){
		$exec = $GLOBALS['module']['prefix'].$func;
		if(function_exists($exec)){
			$out = eval("return ".$exec."();");
			if (error_get_last()!=NULL){
				trigger_error("Error initiation module",E_USER_WARNING);
			}
		}
		else return NULL;				
	}	
	return $out;
}
//-------------------------------------------------------------------------------------------
function coreLoadProperty($section)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$prop = $dbptr->LoadData("SELECT * FROM livProperty WHERE section = '$section'",true,'index','value');
	
	return $prop;
}

//-------------------------------------------------------------------------------------------
function GetShortName($name)
{
	$res =  preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.$3.', $name);
	$res = str_replace(".",". ",$res);
	return mb_convert_case($res, MB_CASE_TITLE, "UTF-8");
}


//-------------------------------------------------------------------------------------------
function num2str($num,$gender=0,$morphonly=false) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одно','двух','трёх','четырёх','пяти','шести','семи', 'восьми','девяти')
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('' ,'' ,'',	 1),
		array(''   ,''   ,''    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode(',',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			if(!$gender)$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

//-------------------------------------------------------------------------------------------
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}

function morphA($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f5.$f2;
	if ($n==1) return $f5.$f1;
	return $f5;
}


?>