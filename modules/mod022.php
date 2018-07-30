<?php
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");

	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : doNope;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
		
	
	$result = Array('result'=>false,'message'=>'','useform'=>false);
	
	$dbptr = $GLOBALS['dbptr'];
	

	
	switch($command){
		case doSave:
			$vals = json_decode($data,true);
			
			
			//$result['message'] .= print_r($vals,true);
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
							 $cng[$ix]['dsc'] = $vals['dsc'.$ix];
						}
					break;
					case "new":
						if($val){
							 $new[$ix]['key'] = $vals['key'.$ix];
							 $new[$ix]['val'] = $vals['val'.$ix];
							 $new[$ix]['dsc'] = $vals['dsc'.$ix];
						}
					break;
				}

			}
			
			
					
//Удаляем удаленные параметры---------------------------------------------------------------------
			$err = 0;
			if(count($del)){
				$query = "DELETE FROM sysSettings WHERE id IN(".implode(',',$del).")";
				if(!$dbptr->Query($query)) $err++;
				else $result['message'] .= "удалено ".count($del)." свойство";
			}
//Изменяем  параметры------------------------------------------------------------------------------

			if(count($cng)){
				foreach($cng as $key => $val){
					$query = "UPDATE sysSettings  SET name='".$val['key']."', value='".$val['val']."',description='".$val['dsc']."'  WHERE id=$key";
					if(!$dbptr->Query($query)) $err++;
					else $result['message'] .= "изменено свойство ".$val['key'];
				}
					
			}
//Добавляем  параметры------------------------------------------------------------------------------

			if(count($new)){
				foreach($new as $key => $val){
					if($val['key']!="" && $val['val']!=""){
						$query = "INSERT INTO sysSettings  (name,value,description) VALUES('".$val['key']."','".$val['val']."','".$val['dsc']."')";
						if(!$dbptr->Query($query)) $err++;
						else $result['message'] .= "добавленно свойство ".$val['key'];
					}
				}
					
			}
//--------------------------------------------------------------------------------------------------
			
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
	}

	unset($_REQUEST['ajax']);
	unset($_REQUEST['mode']);
	unset($_REQUEST['data']);
	
	echo json_encode($result);	
}
else{
$set = $dbptr->LoadData("SELECT * FROM sysSettings WHERE description<>''");
?>
<br>
<form class='input-panel settings-panel' method='POST'  id='settingsForm'>
<table>
<tr>
	<th width='5%'>№ п/п</th>
	<th width='15%'>Свойство</th>
	<th width='50%'>Значение</th>
	<th>Описание</th>
	<th width='5%'>-/-</th>
</tr>	
<?php
	$ix = 1;
	foreach($set as $index => $property){
		$key = $property['name'];
		$val = $property['value'];
		$pid = $property['id'];
		$descr = $property['description'];
		
		echo "<tr>";
			echo "<td>$ix</td>";
			echo "<td>";
			echo "<input type='hidden' value='0' id='del$pid'/><input type='hidden' value='0' id='cng$pid'/>";
			echo "<input type='text' class='green-text' value='$key' id='key$pid' oninput='changeString($pid);'/></td>";
			echo "<td><input type='text' class='green-text' value='$val' id='val$pid' oninput='changeString($pid);'/></td>";
			echo "<td><input type='text' class='green-text' value='$descr' id='dsc$pid' oninput='changeString($pid);'/></td>";
			echo "<td>";
			echo "<button type='button' class='small-button' onclick='delString($pid);' >
					<div class='button-icon  button-icon-delete'></div>
				</button>";
				
			echo "</td>";
		echo "</tr>";
		
		$ix++;
	}
?>
<tr  id='newProperty'>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td>
		<button  type='button'  class='small-button' onclick='addNewProperty();'>
			<div class='button-icon  button-icon-add'></div>
		</button>
	</td>
</tr>			
</table>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('modules/mod022.php','settingsForm','<?php echo "&command=".doSave ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
</form>

<script>
	function changeString(id)
	{
		$('#val' + id).removeClass('green-text');
		$('#key' + id).removeClass('green-text');
		$('#val' + id).addClass('red-text');	
		$('#key' + id).addClass('red-text');
		$('#cng' + id).val(1);		
		
	}
	
	function delString(id){
		
		if($('#del' + id).val() == 1 ){
			$('#val' + id).removeClass('crossed-out-text');
			$('#key' + id).removeClass('crossed-out-text');
			$('#del' + id).val(0);
		}
		else{
			$('#val' + id).addClass('crossed-out-text');
			$('#key' + id).addClass('crossed-out-text');
			$('#del' + id).val(1);
		}		
	}
	
	
	
	var newID = 99999999;
	function addNewProperty()
	{
		str = "<tr><td>-</td><td><input type='hidden' value='1' id='new" + newID + "'/><input type='text'  id='key" + newID + "' placeholder='Новое свойство'/></td><td><input type='text' id='val" + newID + "' placeholder='Новое значение'/><td><input type='text' id='dsc" + newID + "' placeholder='Новое описание'/></td><td>-/-</td></tr>";
		$('#newProperty').before(str);
		newID--;
	}
</script>

<?php 
}
?>