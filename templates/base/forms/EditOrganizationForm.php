<form class='form-default-panel edit-org-panel' method='POST'  id='organizationForm'>
<table>
<tr>
	<th>№ п/п</th>
	<th>Свойство</th>
	<th>Значение</th>
	<th>-/-</th>
</tr>	

<tr>
	<td>-/-</td>
	<td>Название:</td>
	<td><input type='text' value='<?= $Form['orgname'] ?>' id='org-name'/></td>
	<td></td>
</tr>
<tr>
	<td>-/-</td>
	<td>Тип компании:</td>
	<td><select id='org-type'>
	<?php
		foreach($Form['orgtypes'] as $key=>$val){
			if($key == $Form['orgtype']) echo "<option value='$key' selected>$val</option>";
			else						 echo "<option value='$key'>$val</option>";
		}	
	?>
	</select>
	</td>
	<td></td>
</tr>
<tr>
<td>-/-</td>
	<td>Описание:</td>
	<td><input type='text' value='<?= $Form['orgdescription'] ?>' id='org-description'/></td>
	<td></td>
</tr>

<?php
	$ix = 1;
	foreach($Form['orgproperty'] as $index => $property){
		$key = $property['description'];
		$val = $property['property'];
		$pid = $property['id'];
		echo "<tr>";
			echo "<td>$ix</td>";
			echo "<td>";
			echo "<input type='hidden' value='0' id='del$pid'/><input type='hidden' value='0' id='cng$pid'/>";
			echo "<input type='text' class='green-text' value='$key' id='key$pid' oninput='changeString($pid);'/></td>";
			echo "<td><input type='text' class='green-text' value='$val' id='val$pid' oninput='changeString($pid);'/></td>";
			echo "<td>";
			echo "<button type='button' class='small-button' onclick='delString($pid);' >
					<div class='button-icon  button-icon-delete'></div>
				</button>";
				
			echo "</td>";
		echo "</tr>";
		
		$ix++;
	}
?>
<tr  id='newOrgStringProperty'>
	<td></td>
	<td></td>
	<td></td>
	<td>
		<button  type='button'  class='small-button' onclick='addNewOrgProperty();'>
			<div class='button-icon  button-icon-add'></div>
		</button>
	</td>
</tr>			
</table>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','organizationForm','<?= $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
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
	function addNewOrgProperty()
	{
		str = "<tr><td>-</td><td><input type='hidden' value='1' id='new" + newID + "'/><input type='text' id='key" + newID + "' placeholder='Новое свойство'/></td><td><input type='text' id='val" + newID + "' placeholder='Новое значение'/></td><td>-/-</td></tr>";
		$('#newOrgStringProperty').before(str);
		newID--;
	}
</script>


<?php

?>