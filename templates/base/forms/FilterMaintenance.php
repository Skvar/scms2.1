<form class='form-default-panel' method='POST'  id='mntsFilterForm' oninput='onCalcForm();'>

<fieldset>
	<legend>Выборка текущего ремонта:</legend>
	<fieldset class='fieldset-divider'>
		<legend>По адресу:</legend>
		<table>	
		<tr>
			<td><label>Улица:<select id='adr-street'>
			<option value='0'>Не указано</option>
			<?php
					$strname = "";
					foreach($Form['streets'] as $ix => $street){
							echo "<option value=".$street['id'].">".$street['street']."</option>";
					}
				?>	
			</select></label></td>
			<td><label>Дом:<input type='number' id='adr-house' value=''></label></td>
		</tr>		
		</table>
	</fieldset>
	<fieldset class='fieldset-divider'>
		<legend>По статусу:</legend>
		<table>	
		<tr>
			<td><label>Статус:<select id='stat-type'>
			<option value='0'>Не указано</option>
			<?php
				foreach($Form['statuses'] as $ix => $status){
					echo "<option value=".$ix.">".$status."</option>";
				}

			?>			
			</select></label></td>
		</tr>		
		</table>
	</fieldset>	
	<fieldset class='fieldset-divider'>
		<legend>Период:</legend>
		<table>	
		<tr>
			<td><label>С:<input type='month' id='date-begin' value='<?= $Form['filter-date-beg']?>'></label></td>
			<td><label>По:<input type='month' id='date-end' value='<?= $Form['filter-date-end']?>'></label></td>
		</tr>		
		</table>
	</fieldset>	
	<fieldset class='fieldset-divider'>
		<legend>Дополнительно:</legend>
		<table>	
		<tr>
			<td><label>Просроченая дата плана:
				<input type='checkbox' id='opt1' value='0'>	
			</label></td>
		</tr>		
		</table>
	</fieldset>		
</fieldset>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','mntsFilterForm','<?= $Form['arg']; ?>');">
	<div class='button-icon button-icon-find'></div>
	Поиск
</button>
</form>