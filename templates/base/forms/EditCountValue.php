<form class='input-panel adm1-panel' method='POST'  id='ceditForm' oninput='onCalcForm();'>
<table>
<tr>
	<th>Предыдущее показание</th>
	<th>Текущее показание</th>
	<th>Расход</th>
	
</tr>	
<tr>
	<td><input type='number' readonly id='lastValue' value='<?= $Form['last']['value']; ?>'></td>
	<td><input type='number' id='trueValue' value='<?= $Form['cur']['value']; ?>'></td>
	<td><input type='number' readonly id='rate' value='<?= ($Form['cur']['value']-$Form['last']['value']); ?>'></td>
</tr>	
	
</table>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?= $Form['handler']; ?>','ceditForm','<?= $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>


<script>
function onCalcForm()
{
	last = $('#lastValue').val();
	now = $('#trueValue').val();
	
	$('#rate').val(Number(now-last).toFixed(2));	
}

</script>


<?php

?>