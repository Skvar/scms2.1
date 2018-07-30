<form class='form-default-panel edit-service-panel' method='POST'  id='serviceForm'>

	<fieldset>
		<legend>Название</legend>
		<input type='hidden' id='service-id' value='<?= $Form['id']; ?>'>
		<p><input type='text' id='srv-name' value='<?= $Form['name']; ?>'/></p>
		<p><label for='srv-notice'>Заметка для услуги .</label>
		<input type='text' id='srv-notice' value='<?= $Form['notice']; ?>'/></p>
		
	   
	</fieldset>
	<fieldset>
		<legend>Параметры</legend>
		<div class='stuff-table-row'>
			<div class='stuff-table-cell'>
				
				<label for='srv-root'>Родительская группа услуги.</label>
					<p><select id='srv-root'>
					<?php
						echo "<option value=0>Нет</option>";
						foreach($Form['services'] as $id => $srvname){
							if($Form['root'] == $id) 	echo "<option value=$id selected>$srvname</option>";
							else						echo "<option value=$id>$srvname</option>";
						}

					?>		
					</select></p>
				</div>
				<div class='stuff-table-cell'>	
					<label for='srv-base'>Базовая услуга.</label>
					<p><select id='srv-base'>
					<?php
						echo "<option value=0>Нет</option>";
						foreach($Form['services'] as $id => $srvname){
							if($Form['base'] == $id) 	echo "<option value=$id selected>$srvname</option>";
							else						echo "<option value=$id>$srvname</option>";
						}

					?>		
					</select></p>
				</div>
			</div>
			<div class='stuff-table-row'>
				<div class='stuff-table-cell'>
					<label for='srv-interval'>Период действия услуги.<p><input type='text' id='srv-interval' value='<?= $Form['interval']; ?>'/></p></label>
				</div>
				<div class='stuff-table-cell'>
					<label for='srv-measure'>Ед. изм. для услуги<p><input type='text' id='srv-measure' value='<?= $Form['unit']; ?>'/><p></p></label>	
				</div>
			</div>
			<div class='stuff-table-row'>
				<div class='stuff-table-cell'>		
					<label for='srv-calctype'>Тип расчета услуги.</label>
					<p><select id='srv-calctype'>
					<?php
						foreach($Form['calctypes'] as $ix => $type){
							if($Form['calcsrv'] == $ix) 	echo "<option value='$ix' selected>$type</option>";
							else						echo "<option value='$ix'>$type</option>";
						}
					?>		
					</select></p>
				</div>
				<div class='stuff-table-cell'>
					<label for='srv-calcmeasure'>Ед. изм. для расчитаной услуги<p><input type='text' id='srv-calcmeasure' value='<?= $Form['calcunit']; ?>'/></p></label>
				</div>
			</div>
	</fieldset>
	<fieldset style='background-color:#D0D0D0;'>
		<legend>Стоимость</legend>
		<div id='prices-container' class='prices-container'>
		<?php
			include("EditServicePriceForm.php");
		?>
		</div>
	</fieldset>

	<hr>
	<button id='ok-button' type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','serviceForm','<?= $Form['arg']; ?>');">
		<div class='button-icon button-icon-ok'></div>
		Сохранить
	</button>
	<button id='cancel-button' type='button' class='normal-button' onclick="CloseForm();">
		<div class='button-icon button-icon-back'></div>
		Закрыть
	</button>
</form>
<script>

		
</script>