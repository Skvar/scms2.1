<?php
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : 0;
	
	$user = GetCurrentUser();
	$result = Array('result'=>false,'message'=>'');
	
	if($user['id']){
		$accountID = $user['account'];
		$date = date("Y-m-d");	
		switch($command){
			case doAdd:		
			case doEdit:
				$Form['header'] = "";			
				$Form['post'] = "";
				$Form['date'] = date('Y-m-d');
				$Form['broadcast'] = 1;
				$Form['lock'] = 0;
				$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
				if($id){
					$mess = $dbptr->LoadRow("SELECT id,header,message,date as postdate,livUserMessages.lock,broadcast FROM livUserMessages WHERE id=$id");	
					$Form['header'] = $mess['header'];			
					$Form['post'] = $mess['message'];
					$Form['date'] = $mess['postdate'];
					$Form['broadcast'] = $mess['broadcast'];
					$Form['lock'] = $mess['lock'];
				}
							
					$Form['handler'] = "modules/mod024.php";
					$Form['arg'] = "&command=".doSave."&recid=".$id;
					
					$result['message'] .= corePutForm("EditMessage.php",$Form);			
					$result['header'] = "Сообщение для пользователей";
					$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
					$result['user'] = $user['name'];
					$result['useform'] = true;	
					$result['result'] = true;
				
			break;
			case doSave:
				$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
				$vals = json_decode($data,true);
				$recid = isset($_REQUEST['recid']) ? $_REQUEST['recid'] : 0;
				$header = $vals['msg-header'];
				$post	= $vals['msg-text'];
				$date 	= $vals['msg-date'];
				$lock   = intval($vals['msg-lock']);
				$broadcast = intval($vals['msg-broadcast']);			
				
				if($recid)	$query = "UPDATE livUserMessages SET header='$header', message='$post',date='$date', livUserMessages.lock=$lock,broadcast=$broadcast WHERE id=$recid";
				else		$query = "INSERT livUserMessages (user,header,message,date,livUserMessages.lock,broadcast) VALUES(0,'$header','$post','$date',$lock,$broadcast)";
				
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
				$recid = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
				$query = "DELETE FROM livUserMessages WHERE id=$recid";
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
		
		
		unset($_REQUEST['ajax']);
		unset($_REQUEST['mode']);
		unset($_REQUEST['data']);
	}	
	echo json_encode($result);	
}
else{

echo "<br>";
$out['icon'] = './templates/base/images/icon_street.png';
$out['header'] = "Общие сообщения для всех";

$query = "SELECT id,header as postheader,message as post,date as postdate FROM livUserMessages WHERE broadcast=1";
$mess = $dbptr->LoadData($query);

for($a=0;$a<count($mess);$a++){
		$out = $mess[$a];			
		$out['handler'] = 'modules/mod024.php';
		tInsertPost($out);
}

}
?>