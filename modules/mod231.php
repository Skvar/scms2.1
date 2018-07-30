<?php

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
	$orgTypes = coreLoadProperty("orgTypes");
	
	switch($command){
		case doEdit:
			$org = $dbptr->LoadRow("SELECT * FROM livOrganizations WHERE id=$id");
			$org['property'] = $dbptr->LoadData("SELECT id,description	,property FROM livObjProperty WHERE owner = $id AND type=0");	
		
		case doAdd:		
			$Form['orgname'] 		= isset($org['name'])		? $org['name'] 			: "";
			$Form['orgtype'] 		= isset($org['type'])		? $org['type'] 			: "";
			$Form['orgdescription'] = isset($org['description'])? $org['description'] 	: "";
			$Form['orgproperty']	= isset($org['property'])	? $org['property'] 		: Array();
			$Form['orgtypes'] 		= $orgTypes;
	
			$Form['handler'] = "modules/mod231.php";
			$Form['arg'] = "&command=".doSave."&id=".$id.'&type='.$type;
			
			$result['message'] = corePutForm("EditOrganizationForm.php",$Form);
			$result['header'] = isset($org['name']) ? "Редактирование: ".$org['name'] : "Добавление новой организации";
			$result['headericon'] = 'templates/base/images/icon_pub.png';
			$result['user'] = $user['name'];
			$result['useform'] = true;	
			$result['result'] = true;
		break;
		case doSave:
			$vals = json_decode($data,true);
			
			$orgname = isset($vals['org-name']) ?  $vals['org-name'] : "";
			$orgdescription = isset($vals['org-description']) ?  $vals['org-description'] : "";
			$orgtype = isset($vals['org-type']) ?  $vals['org-type'] : 5;
			$orghidden = isset($vals['org-hidden']) ?  $vals['org-hidden'] : 0;
			
			unset($vals['org-name'],$vals['org-description'],$vals['org-type'],$vals['org-hidden']);
			
			$new = Array();
			$cng = Array();
			$del = Array();
				
			foreach($vals as $key => $val){			
				$px = substr($key,0,3);
				$ix = substr($key,3,10);
				switch($px){
					case "del":
						if($val) $del[] = $ix;
					break;
					case "cng":
						if($val){
							 $cng[$ix]['key'] = $vals['key'.$ix];
							 $cng[$ix]['val'] = $vals['val'.$ix];
						}
					break;
					case "new":
						if($val){
							 $new[$ix]['key'] = $vals['key'.$ix];
							 $new[$ix]['val'] = $vals['val'.$ix];
						}
					break;
				}

			}
			
//Сохраняем/добавляем организацию---------------------------------------------------------------
			if($id){	
				$query = "UPDATE livOrganizations SET type=$orgtype, name='$orgname',description='$orgdescription',hidden=$orghidden WHERE id=$id";
				if($dbptr->Query($query)){
					$result['message'] .= "организация сохранена успешно<br>";
				}
			}
			else{
				$query = "INSERT livOrganizations (type,name,description,hidden) VALUES($orgtype,'$orgname','$orgdescription',$orghidden)";
				if($dbptr->Query($query)){
					$result['message'] .= "организация сохранена успешно<br>";
					$id = $dbptr->LastRecord;
				}
			}
			
			
//Удаляем удаленные свойства---------------------------------------------------------------------
			$err = 0;
			if(count($del)){
				$query = "DELETE FROM livObjProperty WHERE id IN(".implode(',',$del).")";
				if(!$dbptr->Query($query)) $err++;
				else $result['message'] .= "удалено ".count($del)." свойство";
			}
//Изменяем  свойства------------------------------------------------------------------------------

			if(count($cng)){
				foreach($cng as $key => $val){
					$query = "UPDATE livObjProperty  SET description='".$val['key']."', property='".$val['val']."'  WHERE id=$key";
					if(!$dbptr->Query($query)) $err++;
					else $result['message'] .= "изменено свойство ".$val['key'];
				}
					
			}
//Добавляем  свойства------------------------------------------------------------------------------
			if(count($new)){
				foreach($new as $key => $val){
					if($val['key']!="" && $val['val']!=""){
						$query = "INSERT INTO livObjProperty  (owner,type,name,description,property) VALUES($id,0,'','".$val['key']."','".$val['val']."')";
						if(!$dbptr->Query($query)) $err++;
						else $result['message'] .= "добавленно свойство ".$val['key'];
					}
				}
					
			}
	
			if($err){
				$result['reload'] = false;
				$result['message'] .= $data;
				$result['message'] .= "Ошибка сохранения<br>".$query;
			}
			else{
				$result['reload'] = true;
				$result['result'] =  true;
			}			
		break;
		
		case doDelete:	
			$query = "DELETE FROM livOrganizations WHERE id=$id";
			if($dbptr->Query($query)){
				$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
				$result['reload'] = true;
				$result['result'] = true;		
			}
			else{
				$result['reload'] = false;
				$result['message'] = "Ошибка удаления<br>".$query;
			}	
		break;
	}

	unset($_REQUEST['ajax'],$_REQUEST['data']);
	
	echo json_encode($result);
}
else{
	echo "<br>";	
	$out['postheader'] = $tmp['name'];
	$out['post'] = "";
	foreach($tmp['property'] as $key=>$val){
			$out['post'] .= "<b>".$key."</b>: ".$val."<br>";	
	}
	unset($tmp);
	$out['post'] .= "<hr>";
	$tmp = $dbptr->LoadRow("SELECT COUNT(id) allhouses FROM livBuildLicense WHERE enddate > NOW()");
	$out['post'] .= "<b>Домов в управлении: </b>".$tmp['allhouses']." ".morphA($tmp['allhouses'],"ов","а","дом")."<br>";
	
	
	
	tInsertPost($out,'orgblock',false);
	
	
	
	
	
}
?>