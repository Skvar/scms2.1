<?php

//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	$find = isset($_REQUEST['find']) ? $_REQUEST['find'] : 0;
	
	$result = Array('result'=>true,'message'=>'','useform'=>false);
	
	$dbptr = $GLOBALS['dbptr'];

	
	switch($find){
		case 1:
		
			$query = "SELECT * FROM sysUserAuth user JOIN livComAccounts acc on acc.id=user.account WHERE acc.number=$data";
		break;
		case 2:
			
			$query = "SELECT * FROM sysUserAuth user JOIN livComAccounts acc on acc.id=user.account WHERE  username like '%$data%'";
		break;
		case 3:
			
			$query = "SELECT * FROM livComAccounts acc JOIN sysUserAuth user on acc.id=user.account WHERE location like '%$data%'";
		break;
		default:
			$result['message'] = "Ошибка";
		
	}
	
	$res = $dbptr->LoadData($query);
	
	foreach($res as $ix =>$val){

		$result['message'] .= $val['number'].", ".$val['location']."; ".$val['employer']."; ".$val['login']."; ".$val['lastlogin']."; ".$val['lastip']."<br>";
	}
	
	
	unset($_REQUEST['ajax']);
	unset($_REQUEST['$find']);
	unset($_REQUEST['data']);
	
	echo json_encode($result);
}
else{
?>
<br>
<form class='adm1-panel finduser-panel' method='POST' id='finduserForm'>
<fieldset>
		<legend>Поиск пользователя</legend>
		<label for='findnumber'>Поиск по номеру<br><input type='text' value='' id='findnumber'><button type='button' class='small-button' onclick='findUser("&find=1",$("#findnumber").val())'><div class='button-icon button-icon-find'></div></button></label>
		<label for='findsurname'>Поиск по фамилии<br><input type='text' value='' id='findsurname'><button type='button' class='small-button' onclick='findUser("&find=2",$("#findsurname").val())'><div class='button-icon button-icon-find'></div></button></label>
		<label for='findaddress'>Поиск по адресу<br><input type='text' value='' id='findaddress'><button type='button' class='small-button' onclick='findUser("&find=3",$("#findaddress").val())'><div class='button-icon button-icon-find'></div></button></label>
</fieldset>
<fieldset>
	<legend>Результат поиска</legend>
	<div id='finduserout'></div>
</fieldset>	
	
</form>

<script>

function findUser(arg,p1)
{
	SendRequest('modules/mod023.php','ajax=1' + arg + '&data='+p1,function(req){
		if(req.responseText.length > 0){
			try{
	        	 obj = JSON.parse(req.responseText);
	        	 if(obj['result'] == true){	
	        	 	$('#finduserout').html(obj['message']);	   	
					return true;		
				} 
	       	}    
	        catch (CatchException){
	        	 console.log(req.responseText);
	        }						
		}
	});	
}
</script>

<?php
}
?>