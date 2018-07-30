<form class='form-default-panel' method='POST'  id='editlicense-panel'>

<fieldset>
<legend>Параметры лицензии</legend>
<div class='stuff-table-row'>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<input type='hidden'  id='lic-owner' value='<?= $Form['lic-owner']; ?>'>
			<label for='lic-number'>Номер лицензии:<p><input type='text'  id='lic-number' value='<?= $Form['lic-number']; ?>'/></p></label>
		</div>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label>Дата открытия:<p><input type='date' id='lic-open'  value='<?= $Form['lic-begdate']; ?>'/></p></label>
		</div>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label>Дата закрытия:<p><input type='date' id='lic-close' value='<?= $Form['lic-enddate']; ?>'/></p></label>
		</div>
</div>
</fieldset>
<fieldset>
<legend>Описание лицензии</legend>
		<label>Описание лицензии:
			<textarea id='lic-description' style='height:100px;'><?= $Form['lic-description']; ?></textarea>
		</label>
		<label>Приказ:
			<textarea id='lic-order' style='height:100px;'><?= $Form['lic-order']; ?></textarea>
		</label>
</fieldset>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','editlicense-panel','<?= $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>
