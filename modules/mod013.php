<?php

if(isset($_REQUEST['ajax'])){
	include("../ajax.php");
 
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : Array();
	
	$user = GetCurrentUser();
	$accountID = $user['account'];
	
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	$values = json_decode($data,true);
	
	
	$userInfo = $dbptr->LoadRow("SELECT * FROM sysUserAuth WHERE id=".$user['id']);
	
	if($userInfo['pwd'] == md5($values['userpwdold'])){
		
		$np = md5($values['userpwd']);
		if($dbptr->Query("UPDATE sysUserAuth SET pwd='$np' WHERE id=".$user['id'])){
			$dbptr->Query("INSERT INTO sysUserAuthHistory (userid,oldpwd,newpwd) VALUES(".$user['id'].",'".$userInfo['pwd']."','$np')");
	
			include("forms/messagebox.php");
			$msgstring = "Пароль успешно сменен.";
			$msgbuttons = Array(msgButtonBack=>'location.reload()'); 
			$result['message'] = MessageBox($msgstring,$msgbuttons,false);
			$result['result'] = true;
			$result['output'] = 'pwd-change';
			//$result['reload'] = true;
		}	
		else{
			$result['message'] = "Ошибка.";
			$result['output'] = "msgline";
			$result['result'] = false;
		}
	}
	else{
		$result['message'] = "Неправильно указан старый пароль.";
		$result['output'] = "msgline";
		$result['result'] = true;
	}
	
	
	unset($_REQUEST['ajax']);
	unset($userInfo);
	echo json_encode($result);
	
	
	
}
else{
?>
<br>
<form method='POST' class='input-panel user1-panel' id='pwd-change'>

		 <fieldset>
		 	<legend>Смена пароля</legend>
		 	<table width='80%'>
		 		<tr>
		 			<td>Старый пароль:</td>
		 			<td><input type='password' id = 'userpwdold'/></td>
		 		</tr>
		 		<tr>
		 			<td>Новый пароль:</td>
		 			<td><input type='password' id = 'userpwd'/></td>
		 		</tr>
		 		<tr>
		 			<td>Повторите новый пароль:</td>
		 			<td><input type='password' id = 'userpwdcheck'/></td>
		 		</tr>
	
		 	</table>
		 	<p style='color:red;' id='msgline'></p>
		 	<hr>
			 <div>
				 <button type='button' class='normal-button' onclick='checkDataInp();'>
				 	<div class='button-icon button-icon-ok'></div>
				 	Сохранить
				 </button>
			 </div>
		 </fieldset>
 </form>
 <br>

<script>

	function checkDataInp()
	{	
		op = $('#userpwdold').val();
		np = $('#userpwd').val();
		npc = $('#userpwdcheck').val();
		if(op=='') PopupMessage(document.getElementById('userpwdold'),'Укажите старый пароль');
		else if(np=='') PopupMessage(document.getElementById('userpwd'),'Укажите новый пароль');	
		else if(np != npc) PopupMessage(document.getElementById('userpwdcheck'),'Пароли не совпадают');	
		else{
			ClosePopups();
			WriteFormData("modules/mod013.php",'pwd-change',"");
		}	
	}
</script>
<?php
}
?>