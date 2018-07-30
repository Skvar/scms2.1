<?php
	//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 0;
	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : doNope;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	$dogmode = isset($_REQUEST['dog']) ? $_REQUEST['dog'] : 0;
	$dogclose = isset($_REQUEST['close']) ? $_REQUEST['close'] : 0;
	

	$user = GetCurrentUser();
	$accountID = $user['account'];
	$date = date("Y-m-d");
	
	$result = Array('result'=>false,'message'=>'','useform'=>false);
	
	$Hidden=true;
	include("mod2.php");
	
	$build = buildGetList($id);
	$dbptr = $GLOBALS['dbptr'];
	
	switch($command){
		case doSave:	
			if($dogmode){
				$vals = json_decode($data,true);
				$dognum = $vals['dog-number'];
				$dogstart	= $vals['date-open'];
				$dogend 	= $vals['date-close'];
				$dogdes  = $vals['close-description'];
				
				$query = "UPDATE livBuildLicense SET dognum='$dognum',begdate='$dogstart',enddate='$dogend',closedescription='$dogdes' WHERE id=".$id;
				if($dbptr->Query($query)){
					$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
					$result['reload'] = true;
					$result['result'] = true;
							
				}
				else{
					$result['reload'] = false;
					$result['message'] = "Ошибка сохранения<br>".$query;
				}		
			}	
			else{
				if($id){
					
					
					$vals = json_decode($data,true);
					$street 	= $vals['street'];
					$streetname = $vals['strname'];
					$buildnum 	= $vals['number'];
					$buildname	= $vals['name'];
					$srvorg		= $vals['srvorg'];//
					$year		= $vals['year'];//
					$wmat		= $vals['material'];//
					$entrcount	= $vals['entrance'];//
					$elevcount	= $vals['elevator'];//
					$aptcount	= $vals['numapt'];//
					$square		= $vals['allsquare'];//
					$sharedsquare= $vals['sharedsquare'];//
					$unlivsquare= $vals['unlivsquare'];//
					
					$reqfield=Array('Обслуживающая организация'=>$srvorg,'Год постройки'=>$year,'Подъезды'=>$entrcount,'Этажность'=>$elevcount,'Квартиры'=>$aptcount,'Материал стен'=>$wmat,'Площадь'=>$square,'Площадь общедомового имущества'=>$sharedsquare,'Площадь нежилых помещений'=>$unlivsquare);
//save build information--------------------------------------------------------------------------
					$queryes[] = "UPDATE livBuilding SET root=$street,street='$streetname',number='$buildnum',name='$buildname',contract=$srvorg WHERE id=$id";
					foreach($reqfield as $key =>$val){
						$queryes[] = "UPDATE livObjProperty SET property='$val' WHERE owner=$id AND description='$key'";
					}
					$err = 0;
					foreach($queryes as $index =>$query){
						if(!$dbptr->Query($query)) $err++;
					}
					
					if(!$err){
						$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
						$result['reload'] = true;
						$result['result'] = true;		
					}
					else{
						$result['reload'] = false;
						$result['message'] = "Ошибка сохранения<br>".$query;
					}		
				}	
			}
		break;
		case doEdit:	
			if($dogmode){
				$isclosed = false;
				$dog = $dbptr->LoadRow("SELECT * FROM livBuildLicense WHERE build=$id AND NOW() >= begdate AND NOW() <= enddate");
				if(!is_array($dog)){
					$dog = $dbptr->LoadRow("SELECT * FROM livBuildLicense WHERE build=$id");
					$isclosed = true;
				}
				$Form['dognumber'] 			= isset($dog['dognum'])	? $dog['dognum'] 	: 0;
				$Form['dateopen'] 			= isset($dog['begdate'])	? $dog['begdate'] 	: date("Y-m-d");
				
				if($dogclose) 	$Form['dateclose'] = date("Y-m-d");
				else 			$Form['dateclose'] = isset($dog['enddate'])	? $dog['enddate'] : date("Y-m-d");
				
				$Form['closedescription'] 	= isset($dog['closedescription']) 	? $dog['closedescription'] : "";
			
				$Form['handler'] = "modules/mod210.php";
				$Form['arg'] = "&command=".doSave."&id=".$dog['id']."&dog=1";
				$Form['build'] = $build;
				$result['message'] = corePutForm("EditBuildDogForm.php",$Form);
				if($dogclose) 	 $result['header'] = "Закрыть договор управления домом ".$build['name'];
				else			 $result['header'] = "Договор управления домом ".$build['name'];
				$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
				$result['user'] = $user['name'];
				$result['useform'] = true;	
				$result['result'] = true;
			}
			else{
				$isclosed = false;

				$Form['streets'] 	= $dbptr->LoadData("SELECT id,street FROM livBuilding WHERE type = 1");
				$Form['contractors'] 	= $dbptr->LoadData("SELECT id,name FROM livOrganizations WHERE type = 2");
				$Form['materials']		= $dbptr->LoadData("SELECT livProperty.index,value FROM livProperty WHERE section='wallTypes'");
				$Form['handler'] = "modules/mod210.php";
				$Form['arg'] = "&command=".doSave."&id=".$id;
				$Form['build'] = $build;
				$result['message'] = corePutForm("EditBuildForm.php",$Form);
				$result['header'] = "Редактирование параметров дома ".$build['name'];
				$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
				$result['user'] = $user['name'];
				$result['useform'] = true;	
				$result['result'] = true;
				
			}
		break;
	}
	
	
	//print_r($Form['contractors']);
	
	unset($_REQUEST['ajax']);
	unset($_REQUEST['mode']);
	unset($_REQUEST['data']);
	
	echo json_encode($result);
	
}
else{

		$out['postheader'] = $build['name'];
		$out['post'] = "";
		
		$prop = $build['property'];
		$srvorg = loadOrganizations($prop['Обслуживающая организация']);
		$mat = coreLoadProperty("wallTypes");
		
		
		
//Common information for build-------------------------------------------------------------------------------
		$out['post'] = num2str($prop['Этажность'],2)."этажный, ".num2str($prop['Подъезды'],2)."подъездный, ".$mat[$prop['Материал стен']]." дом, сдан в эксплуатацию в ".$prop['Год постройки']." году, 
		содержит ".$prop['Квартиры']." ".morphA($prop['Квартиры'],"у","ы","квартир")." общей площадью ".round($prop['Площадь'],2)." кв.м.
		 ".($prop["Площадь нежилых помещений"] ? ", также содержит нежилые помещения площадью ".round($prop['Площадь нежилых помещений'],2)." кв.м.":"").
		 " Площадь общедомового имущества дома составляет ".round($prop['Площадь общедомового имущества'],2)." кв.м.";
		 
		 if(strtotime($build['enddate']) < strtotime(date("Y-m-d"))){
		 	$out['post'] .= "<br><b>Дом вышел из под управления ".$GLOBALS['organization']['name']." ".$build['enddate']." по причине:<br>&nbsp;&nbsp;&nbsp;<i>".$build['closedescription']."</i></b>";
		 }
		 else{
			 $out['post'] .= "<br>Обслуживает дом обслуживающая организация: <b>".$srvorg['name']."</b>".
			 (isset($srvorg['property']['телефон'])? " ,телефоны: ".$srvorg['property']['телефон']:"").
			 (isset($srvorg['property']['Адрес'])? " ,организация находится по адресу: <i>".$srvorg['property']['Адрес']:"")."</i>".
			 (isset($srvorg['property']['Телефон'])? ", телефоны: <b>".str_replace("-","&#8209;",$srvorg['property']['Телефон'])."</b>" : "").".";
		 }
		 
		tInsertPost($out,'building',false);
		

//------------------------------------------------------------------------------------------------------------
		$query = "SELECT DATE_FORMAT(ratedate, '%m.%Y') as ddate,coldRate,hotRate FROM livComResource WHERE houseid=".$build['id']." ORDER BY ratedate DESC";
		$tmp = $dbptr->LoadData($query,false);
		if(count($tmp)>0){
		
			$Histogramm = Array('width'=>900,'height'=>150);
			
			foreach($tmp as $ix => $val){
				$Histogramm['data']['ХВС'][$val['ddate']] = $val['coldRate'];	
				$Histogramm['data']['ГВС'][$val['ddate']] = $val['hotRate'];	
			}
			
			if(is_array($tmp)){
				tInsertContentHeader(Array('pagetitle'=>"Расход ресурсов (м<sup>3</sup>)"),'page-title-var1');	
				include("histogramm.php");
				unset($tmp);
			}
		}
		


//Debitors info for build-------------------------------------------------------------------------------------
		$query = "SELECT DATE_FORMAT(debdate, '%m.%Y') as ddate,summ FROM livBuildDebitors WHERE account=".$build['id']." ORDER BY debdate DESC";
		$tmp = $dbptr->LoadData($query,true,'ddate','summ');
		
		if(is_array($tmp)){
			tInsertContentHeader(Array('pagetitle'=>"Динамика задолжености (руб.)"),'page-title-var1');
			$data['Задолженость'] = $tmp;
	
			$Histogramm = Array('width'=>900,'height'=>150,'data'=>$data);
			include("histogramm.php");
			unset($tmp,$data);
		}

//Files for build--------------------------------------------------------------------------------------------
		$Files = Array('width'=>130,'height'=>160,'filelist'=>'largeicon','show'=>true,'type'=>1,'owner'=>$build['id']);			
		include("files.php");
		echo "<br>";
}
?>