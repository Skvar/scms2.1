<form class='form-default-panel editlicense-panel' method='POST'  id='editlicense-panel'>
<table>
<tr>
	<input type='hidden'  id='lic-owner' value='<?php echo $Form['lic-owner']; ?>'>
	<td><label for='lic-number'>Номер лицензии:</label></td>
    <td><input type='text'  id='lic-number' value='<?php echo $Form['lic-number']; ?>'></td>
</tr>
<tr>
	<td>
		<label>Дата открытия:<input type='date' id='lic-open'  value='<?php echo $Form['lic-begdate']; ?>'></label>
	</td>
	<td>
		<label>Дата закрытия:<input type='date' id='lic-close' value='<?php echo $Form['lic-enddate']; ?>'></label>
	</td>
</tr>
<tr>
	<td colspan='3'>
		<label>Описание лицензии:
			<textarea id='lic-description' style='height:100px;'><?php echo $Form['lic-description']; ?></textarea>
		</label>
	</td>
</tr>	
<tr>
	<td colspan='3'>
		<label>Приказ:
			<textarea id='lic-order' style='height:100px;'><?php echo $Form['lic-order']; ?></textarea>
		</label>
	</td>
</tr>	
</table>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','editlicense-panel','<?php echo $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>
