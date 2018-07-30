<form class='form-default-panel edit-build-panel' method='POST'  id='edit-build-dog-form'>
<fieldset>
	<legend>Договор</legend>

<table>
<tr>
	<td><label for='dog-number'>Номер договора:</label></td>
    <td><input type='text'  id='dog-number' value='<?php echo $Form['dognumber']; ?>'></td>
</tr>
<tr>
	<td>
		<label>Дата открытия:<input type='date' id='date-open'  value='<?php echo $Form['dateopen']; ?>'></label>
	</td>
	<td>
		<label>Дата закрытия:<input type='date' id='date-close' value='<?php echo $Form['dateclose']; ?>'></label>
	</td>
</tr>
</table>
</fieldset>
<fieldset>
	<legend>Причина закрытия</legend>
	<textarea id='close-description' style='height:100px;'><?php echo $Form['closedescription']; ?></textarea>
</fieldset>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','edit-build-dog-form','<?php echo $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>