<?php
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : doNope;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$licid =  isset($_REQUEST['licid']) ? $_REQUEST['licid'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	
	$user = GetCurrentUser();
	$date = date("Y-m-d");
	$dbptr = $GLOBALS['dbptr'];
	$result = Array('result'=>false,'message'=>'','useform'=>false);
	
	switch($command){
		case doEdit:
			$lic = $dbptr->LoadRow("SELECT * FROM livLicense WHERE id=$licid");	
		case doAdd:
			$Form['lic-number'] 	= isset($lic['number'])		? $lic['number'] 		: 0;
			$Form['lic-owner'] 		= isset($lic['owner'])		? $lic['owner'] 		: 0;
			$Form['lic-begdate'] 	= isset($lic['begdate']) 	? $lic['begdate'] 		: $date;
			$Form['lic-enddate'] 	= isset($lic['enddate']) 	? $lic['enddate'] 			: $date;
			$Form['lic-description']= isset($lic['description'])? $lic['description'] 	: "";
			$Form['lic-order']		= isset($lic['order']) 		? $lic['order']			: "";
				
			$Form['handler'] = "modules/mod232.php";
			$Form['arg'] = "&command=".doSave."&licid=".$licid."&id=".$id;
		
			$result['message'] = corePutForm("EditLicenseForm.php",$Form);
			$result['header'] = "Редактирование лицензии №".$Form['lic-number'];
			$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
			$result['user'] = $user['name'];
			$result['useform'] = true;	
			$result['result'] = true;
		break;
		case doSave:
			$vals = json_decode($data,true);
			$number = $vals['lic-number'];
			$begdate	= $dm = date("Y-m-d",strtotime($vals['lic-open']));
			$enddate 	= $dm = date("Y-m-d",strtotime($vals['lic-close']));
			$description  = $vals['lic-description'];
			$order = $vals['lic-order'];
			$owner  = $vals['lic-owner'];
			$userid = $user['id'];
				
			if($licid)	$query = "UPDATE livLicense SET number=$number,begdate='$begdate',enddate='$enddate',description='$description', livLicense.order='$order'  WHERE id=$licid";
			else		$query = "INSERT livLicense (number,owner,begdate,enddate,description,livLicense.order) VALUES($number,$owner,'$begdate','$enddate',$description,$order)";
			
			
			
			if($dbptr->Query($query)){
				$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
				$result['reload'] = true;
				$result['result'] = true;
						
			}
			else{
				$result['reload'] = false;
				$result['message'] = "Ошибка сохранения<br>".$query;
			}		
		break;
		case doDelete:
			/*$query = "DELETE FROM livBuildMaintenance WHERE id=$wid";
			if($dbptr->Query($query)){
				$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
				$result['reload'] = true;
				$result['result'] = true;		
			}
			else{
				$result['reload'] = false;
				$result['message'] = "Ошибка удаления<br>".$query;
			}*/						
		break;
	}
	
	unset($_REQUEST['ajax'],$_REQUEST['$command'],$_REQUEST['data'],$_REQUEST['licid'],$_REQUEST['id']);
	
	echo json_encode($result);	
}
else{
	echo "<br>";

	$out['postheader'] = $tmp['license']['description']." №".$tmp['license']['number'].".";
	$out['post'] = "Выдана ".$tmp['license']['order']." ".$tmp['license']['begdate']."г.";

	tInsertPost($out,'orgblock',false);
							
	$Files = Array('width'=>130,'height'=>160,'filelist'=>'largeicon','show'=>true,'type'=>3,'owner'=>$tmp['license']['id']);					
	include("files.php");
}
?>