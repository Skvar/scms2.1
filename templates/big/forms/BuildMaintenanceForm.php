<form class='input-panel maintenance-panel' method='POST'  id='workForm'>
<table>
<tr>
	<td colspan='3'>
		<label for='work-description'>Описание работ:</label>
	</td>
</tr>
<tr>
	<td colspan='3'>
		<textarea id='work-description' style='height:100px;'><?php echo $Form['work']; ?></textarea>
	</td>
</tr>
<tr>
	<td colspan='3'>
		<label for='work-volume'>Объем работ:</label>
	</td>
</tr>
<tr>
	<td colspan='3'>
		<input type='text'  id='work-volume' value='<?php echo $Form['workvolume']; ?>'>
	</td>
</tr>
<tr>
	<td>
		<label>Стоимость:<input type='number' id='work-bill'  value='<?php echo $Form['bill']; ?>'></label>
	</td>
	<td>
		<label>Подрядчик:
			<select id='work-contractor'>
			<?php

				foreach($Form['contractors'] as $ix => $contractor){
					if($Form['contractor'] == $contractor['id']) 	echo "<option value=".$contractor['id']." selected>".$contractor['name']."</option>";
					else											echo "<option value=".$contractor['id'].">".$contractor['name']."</option>";
				}

			?>
				
			</select>
		</label>
	</td>
	<td>
		<label>Дата публикации:<input type='date' id='work-date' value='<?php echo $Form['postdate']; ?>'></label>
	</td>
</tr>
<tr>
	<td colspan='3'>
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
	</td>
</tr>		
</table>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','workForm','<?php echo $Form['arg']; ?>');">
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