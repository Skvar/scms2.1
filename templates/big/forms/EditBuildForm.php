<form class='form-default-panel edit-build-panel' method='POST'  id='buildeditForm'>

<fieldset>
	<legend>Улица и название</legend>
	<table>
		<tr>
			<td>	
				
				<label for='street'>Улица:
				<select id='street' onchange='changeName()'>
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
				</select>
				</label>
				<input type='hidden' id='strname' value='<?php echo $strname ?>'/>
			</td>		
			</td>	
			<td>	
				<label for='number'>Номер дома:<input id='number' type='text' value='<?php echo $Form['build']['number']  ?>'/></label>	
			</td>
			<td>	
				<label for='name'>Имя:<input id='name' type='text' value='<?php echo $Form['build']['name']  ?>' readonly/></label>			
			</td>			
		</tr>
	</table>
   
</fieldset>
<fieldset>
	<legend>Обслуживающая организация</legend>
	<select id='srvorg'>
	<?php
		foreach($Form['contractors'] as $ix => $contractor){
				if($Form['build']['property']['Обслуживающая организация'] == $contractor['id']) 	echo "<option value=".$contractor['id']." selected>".$contractor['name']."</option>";
				else																		echo "<option value=".$contractor['id'].">".$contractor['name']."</option>";
		}
	?>
	</select>
	</label>
   
</fieldset>
<fieldset>
	<legend>Параметры дома</legend>
	<table>
		<tr>
			<td>	
				<label for='year'>Год постройки:<input id='year' type='text' value='<?php echo $Form['build']['property']['Год постройки']  ?>'/></label>
			</td>		
			</td>	
			<td>	
				<label for='material'>Материал стен:
					<select id='material'>
					<?php
						foreach($Form['materials'] as $ix => $material){
								if($Form['build']['property']['Материал стен'] == $material['index']) 	echo "<option value=".$material['index']." selected>".$material['value']."</option>";
								else															echo "<option value=".$material['index'].">".$material['value']."</option>";
						}
					?>
					</select>
				</label>	
			</td>
			<td>	
			</td>			
		</tr>
		<tr>
			<td>	
				<label for='entrance'>Кол-во подъездов:<input id='entrance' type='text' value='<?php echo $Form['build']['property']['Подъезды']  ?>'/></label>
			</td>		
			</td>	
			<td>	
				<label for='elevator'>Кол-во этажей:<input id='elevator' type='text' value='<?php echo $Form['build']['property']['Этажность']  ?>'/></label>	
			</td>
			<td>	
				<label for='numapt'>Кол-во квартир:<input id='numapt' type='text' value='<?php echo $Form['build']['property']['Квартиры']  ?>'/></label>		
			</td>			
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Площади</legend>
	<table>
		<tr>
			<td>	
				<label for='allsquare'>Общая площадь:<input id='allsquare' type='text' value='<?php echo $Form['build']['property']['Площадь']  ?>'/></label>
			</td>		
			</td>	
			<td>	
				<label for='sharedsquare'>Общедомовое имущество:<input id='sharedsquare' type='text' value='<?php echo $Form['build']['property']['Площадь общедомового имущества']  ?>'/></label>		
			</td>
			<td>	
				<label for='nolivsquare'>Нежилая площадь:<input id='nolivsquare' type='text' value='<?php echo $Form['build']['property']['Площадь нежилых помещений']  ?>'/></label>		
			</td>			
		</tr>
	</table>
</fieldset>

<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','buildeditForm','<?php echo $Form['arg']; ?>');">
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