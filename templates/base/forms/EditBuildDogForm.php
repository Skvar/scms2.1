<form class='form-default-panel' method='POST'  id='edit-build-dog-form'>
<fieldset>
	<legend>Договор</legend>
	<div class='stuff-table-row'>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label for='dog-number'>Номер договора:<p><input type='text'  id='dog-number' value='<?= $Form['dognumber']; ?>'></p></label>
	    </div>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label>Дата открытия:<p><input type='date' id='date-open'  value='<?= $Form['dateopen']; ?>'></p></label>
		</div>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label>Дата закрытия:<p><input type='date' id='date-close' value='<?= $Form['dateclose']; ?>'></p></label>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>Причина закрытия</legend>
	<textarea id='close-description' style='height:100px;'><?= $Form['closedescription']; ?></textarea>
</fieldset>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','edit-build-dog-form','<?= $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>