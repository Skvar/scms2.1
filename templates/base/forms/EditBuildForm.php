<form class='form-default-panel' method='POST'  id='buildeditForm'>

<fieldset>
	<legend>Улица и название</legend>
	<div class='stuff-table-row'>
		<div class='stuff-table-cell stuff-table-cell-3col'>
				<label for='street'>Улица:
				<p><select id='street' onchange='changeName()'>
				<?php
					$strname = "";
					foreach($Form['streets'] as $ix => $street){
							if($Form['build']['root'] == $street['id']){
								 	echo "<option value=".$street['id']." selected>".$street['street']."</option>";
								 	$strname = $street['street'];
							}
							else									echo "<option value=".$street['id'].">".$street['street']."</option>";
					}
				?>
				</select></p>
				</label>
				<input type='hidden' id='strname' value='<?= $strname ?>'/>
			</div>			
			<div class='stuff-table-cell stuff-table-cell-3col'>	
				<label for='number'>Номер дома:<p><input id='number' type='text' value='<?= $Form['build']['number']  ?>'/></p></label>	
			</div>
			<div class='stuff-table-cell stuff-table-cell-3col'>	
				<label for='name'>Имя:<p><input id='name' type='text' value='<?= $Form['build']['name']  ?>' readonly/></p></label>			
			</div>			
	</div>
</fieldset>
<fieldset>
	<legend>Обслуживающая организация</legend>
	<p><select id='srvorg'>
	<?php
		foreach($Form['contractors'] as $ix => $contractor){
				if($Form['build']['property']['Обслуживающая организация'] == $contractor['id']) 	echo "<option value=".$contractor['id']." selected>".$contractor['name']."</option>";
				else																		echo "<option value=".$contractor['id'].">".$contractor['name']."</option>";
		}
	?>
	</select></p>
	</label>
   
</fieldset>
<fieldset>
	<legend>Параметры дома</legend>
	<div class='stuff-table-row'>
		<div class='stuff-table-cell'>
			<label for='year'>Год постройки:<p><input id='year' type='text' value='<?= $Form['build']['property']['Год постройки']  ?>'/></p></label>
		</div>		
		<div class='stuff-table-cell'>	
				<label for='material'>Материал стен:
					<p><select id='material'>
					<?php
						foreach($Form['materials'] as $ix => $material){
								if($Form['build']['property']['Материал стен'] == $material['index']) 	echo "<option value=".$material['index']." selected>".$material['value']."</option>";
								else															echo "<option value=".$material['index'].">".$material['value']."</option>";
						}
					?>
					</select></p>
				</label>	
		</div>	
	</div>			
	<div class='stuff-table-row'>
		<div class='stuff-table-cell stuff-table-cell-3col'>
			<label for='entrance'>Кол-во подъездов:<p><input id='entrance' type='text' value='<?= $Form['build']['property']['Подъезды']  ?>'/></p></label>
		</div>		
		<div class='stuff-table-cell stuff-table-cell-3col'>	
			<label for='elevator'>Кол-во этажей:<p><input id='elevator' type='text' value='<?= $Form['build']['property']['Этажность']  ?>'/></p></label>	
		</div>
		<div class='stuff-table-cell stuff-table-cell-3col'>	
			<label for='numapt'>Кол-во квартир:<p><input id='numapt' type='text' value='<?= $Form['build']['property']['Квартиры']  ?>'/></p></label>		
		</div>			
	</div>
</fieldset>
<fieldset>
	<legend>Площади</legend>
	<div class='stuff-table-row'>
		<div class='stuff-table-cell stuff-table-cell-3col'>	
			<label for='allsquare'>Общая площадь:<p><input id='allsquare' type='text' value='<?= $Form['build']['property']['Площадь']  ?>'/></p></label>
		</div>			
		<div class='stuff-table-cell stuff-table-cell-3col'>		
			<label for='sharedsquare'>Общедомовое имущество:<p><input id='sharedsquare' type='text' value='<?= $Form['build']['property']['Площадь общедомового имущества']  ?>'/></p></label>		
		</div>
		<div class='stuff-table-cell stuff-table-cell-3col'>		
			<label for='unlivsquare'>Нежилая площадь:<p><input id='unlivsquare' type='text' value='<?= $Form['build']['property']['Площадь нежилых помещений']  ?>'/></p></label>		
		</div>			
	</div>
</fieldset>

<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','buildeditForm','<?= $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>
<script>
function changeName()
{
	name = "ул. " + $('#street option:selected').text() + " д. " + $('#number').val();
	
	$('#name').val(name);
		
}
		
</script>




<?php

?>