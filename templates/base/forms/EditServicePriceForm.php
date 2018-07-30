<div class='edit-service-price-panel' method='POST'  id='servicePriceForm'>
		<?php
			if(isset($Form['addprice'])){
				unset($Form['addprice']);
				
		?>		
			<script>BlockForm('serviceForm',true);</script>
			<div class='stuff-table-row'>
				<div class='stuff-table-cell'>
					<input type='hidden' id='service-id' value='<?= $Form['id']; ?>'>
					<input type='hidden' id='price-id' value='<?= $Form['price-service']; ?>'>
					<label>Начало действия тарифа:<p><input id='price-date-beg' type='date' value='<?= $Form['price-date-beg'];?>'></p></label>
				</div>
				<div class='stuff-table-cell'>
					<label>Конец действия тарифа:<p><input  id='price-date-end' type='date' value='<?= $Form['price-date-end'];?>'></p></label>
				</div>
				<div class='stuff-table-cell'>
					<label>Цена:<p><input id='price' type='text' value='<?= $Form['price'];?>'></p></label>
				</div>
				<div class='stuff-table-cell'>
					<label>Объем норматива:<p><input id='price-volume' type='text' value='<?= $Form['price-volume'];?>'></p></label>
				</div>
				<div class='stuff-table-cell'>
					<label>Единица измерения:<p><input id='price-measureunit' type='text' value='<?= $Form['price-measure'];?>'></p></label>
				</div>
				<div class='stuff-table-cell'>
					<label>Ед. изм. норматива:<p><input id='price-vol-measureunit' type='text' value='<?= $Form['price-vol-measure'];?>'></p></label>
				</div>		
			</div>
			<label>Поставщик:<p><select id='price-provider'>
				<option value='0'>-</option>
				<?php
					foreach($Form['providers'] as $id => $name){
						if($id==$Form['price-provider'])	echo "<option value='$id' selected>$name</option>";
						else 						echo "<option value='$id'>$name</option>";
					}
				?>		
				</select></p></label>
				<label>Дополнительные сведения:<p><textarea id='price-description'><?= $Form['price-description']; ?></textarea></p></label>			
					<button  type='button' class='strait-button' onclick="WriteFormData('<?= $Form['handler']; ?>','servicePriceForm','<?= $Form['arg']; ?>&ajax=1&command=<?= doSavePrice; ?>');">
						Ок.
					</button>
					<button  type='button' class='strait-button' onclick="Commandsd('<?= $Form['handler']; ?>','&id=<?= $Form['id'] ?>&ajax=1&command=<?= doShowPrice; ?>');">
						Отмена
					</button>
					<?php if($Form['price-id']){ ?>
				
					<button  type='button' class='strait-button' onclick="Commanddel('<?= $Form['handler']; ?>','<?= $Form['arg']; ?>&ajax=1&command=<?= doDeletePrice; ?>');">
						Удалить
					</button>
				<?php	
					}
			}
			else{
				echo "<script>BlockForm('serviceForm',false);</script>";
				foreach($Form['prices'] as $ix => $price){
						echo "<a href='#' onclick='Commandsd(\"".$Form['handler']."\",\"&id=".$Form['id']."&pid=".$price['pid']."&ajax=1&command=".doEditPrice."\");'><p class='small-text'>".($ix+1)."). Период: ".($price['datebegin']."-".$price['dateend'])." цена: ".$price['price']." норма: ".$price['volume'].$price['measureunit'].
						" поставщик: ".$Form['providers'][$price['provider']].".</p></a>";	
				}
				echo "<p><a href='#' onclick='Commandsd(\"".$Form['handler']."\",\"&id=".$Form['id']."&ajax=1&command=".doAddPrice."\");'>Добавить...</a></p>";	
			}
		?>
</div>