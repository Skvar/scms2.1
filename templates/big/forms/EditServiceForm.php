<form class='input-panel service-panel' method='POST'  id='serviceForm'>

	<fieldset>
		<legend>Название</legend>
		<input type='text' id='srv-name' value='<?php echo $Form['name']; ?>'/>
		<label for='srv-notice'>Заметка для услуги .</label>
		<input type='text' id='srv-notice' value='<?php echo $Form['notice']; ?>'/>
		
	   
	</fieldset>
	<fieldset>
		<legend>Параметры</legend>
					<label for='srv-root'>Родительская группа услуги.</label>
					<select id='srv-root'>
					<?php
						echo "<option value=0>Нет</option>";
						foreach($Form['services'] as $id => $srvname){
							if($Form['root'] == $id) 	echo "<option value=$id selected>$srvname</option>";
							else						echo "<option value=$id>$srvname</option>";
						}

					?>		
					</select>
					<label for='srv-base'>Базовая услуга.</label>
					<select id='srv-base'>
					<?php
						echo "<option value=0>Нет</option>";
						foreach($Form['services'] as $id => $srvname){
							if($Form['base'] == $id) 	echo "<option value=$id selected>$srvname</option>";
							else						echo "<option value=$id>$srvname</option>";
						}

					?>		
					</select>
			
					<label for='srv-interval'>Период действия услуги.<input type='text' id='srv-interval' value='<?php echo $Form['interval']; ?>'/></label>
			
					<label for='srv-measure'>Ед. изм. для услуги<input type='text' id='srv-measure' value='<?php echo $Form['unit']; ?>'/></label>	
				
					<label for='srv-calctype'>Тип расчета услуги.</label>
					<select id='srv-calctype'>
					<?php
						foreach($Form['calctypes'] as $ix => $type){
							if($Form['calcsrv'] == $ix) 	echo "<option value=$id selected>$type</option>";
							else						echo "<option value=$id>$type</option>";
						}

					?>		
					</select>
					<label for='srv-calcmeasure'>Ед. изм. для расчитаной услуги<input type='text' id='srv-calcmeasure' value='<?php echo $Form['calcunit']; ?>'/></label>

	</fieldset>
	<fieldset>
		<legend>Стоимость</legend>
		<table>
			<tr>
				<th width='3%'>№ п/п</th>
				<th width='12%'>Период</th>
				<th width='8%'>Цена</th>
				<th width='8%'>Норма</th>
				<th width='15%'>Ед.Изм</th>
				<th width='15%'>Ед. Изм. нормы</th>
				<th width='15%'>Поставщик</th>
				<th>Примечание</th>	
				<th width='10%'>-/-</th>			
			</tr>	
		<?php
		foreach($Form['prices'] as $ix => $price){
			echo "<tr>";
				echo "<td>$ix</td>";
				echo "<td>".($price['datebegin']."<br>".$price['dateend'])."</td>";
				echo "<td>".$price['price']."</td>";
				echo "<td>".$price['volume']."</td>";
				echo "<td>".$price['measureunit']."</td>";
				echo "<td>".$price['volmeasureunit']."</td>";
				echo "<td>".$price['provider']."</td>";
				echo "<td>".$price['description']."</td>";
				$handler = "modules/mod22.php";
				$arg1 = "&ajax=1&command=".doEditPrice."&id=".$price['id'];
				$arg2 = "&ajax=1&command=".doDeletePrice."&id=".$price['id'];
				echo "<td>
						<div class='service-element-button hierarchy-element-button-edit' onclick='Commandsd(\"$handler\",\"$arg1\");'></div>
						<div class='service-element-button hierarchy-element-button-delete' onclick='Commanddel(\"$handler\",\"$arg2\");'></div>	
					 </td>";
			echo "</tr>";
		}	
		?>
		</table>
	</fieldset>

	<hr>
	<button type='button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','serviceForm','<?php echo $Form['arg']; ?>');">
		<div class='button-icon button-icon-ok'></div>
		Сохранить
	</button>
	<button type='button' class='normal-button' onclick="CloseForm();">
		<div class='button-icon button-icon-back'></div>
		Закрыть
	</button>
</form>
<script>

		
</script>