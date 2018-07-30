<form class='form-default-panel' method='POST'  id='workForm'>
<fieldset>
	<legend>Состав текущего ремонта</legend>
	<label for='work-description'>Описание работ:</label>
	<textarea id='work-description' style='height:100px;'><?= $Form['work']; ?></textarea>
	<label for='work-volume'>Объем работ:</label>
	<input type='text'  id='work-volume' value='<?= $Form['workvolume']; ?>'>

	<p><div class='stuff-table-row'>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label>Стоимость:<p><input type='number' id='work-bill'  value='<?= $Form['bill']; ?>'></p></label>
		</div>
		<div class='stuff-table-cell stuff-table-cell-3col'>
		<label>Подрядчик:
			<p><select id='work-contractor'>
			<?php

				foreach($Form['contractors'] as $ix => $contractor){
					if($Form['contractor'] == $contractor['id']) 	echo "<option value=".$contractor['id']." selected>".$contractor['name']."</option>";
					else											echo "<option value=".$contractor['id'].">".$contractor['name']."</option>";
				}

			?>
				
			</select></p>
		</label>
		</div>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label>Дата публикации:<p><input type='date' id='work-date' value='<?= $Form['postdate']; ?>'></p></label>
		</div>
	</div>
	</p>

	<label>Статус:
		<select id='work-status'>
			<?php
				foreach($Form['statuses'] as $ix => $status){
					if($Form['status'] == $ix) 	echo "<option value=".$ix." selected>".$status."</option>";
					else						echo "<option value=".$ix.">".$status."</option>";
				}
			?>
		</select>
	</label>
</fieldset>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','workForm','<?= $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>


<?php

?>