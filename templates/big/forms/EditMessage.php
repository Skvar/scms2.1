<form class='input-panel messageedit-panel' method='POST'  id='messageForm' >
<div>
		<label for='msg-header'>Заголовок:</label>
		<input type='text'  id='msg-header' value='<?php echo $Form['header']; ?>'>
</div>
<div>
		<label for='msg-text'>Текст поста:</label>
		<textarea id='msg-text' style='height:100px;'><?php echo $Form['post']; ?></textarea>
</div>
<div>
		<label>Дата публикации:<input type='date' id='msg-date' value='<?php echo $Form['date']; ?>'></label>
</div>
<fieldset>
	<legend>Параметры сообщения</legend>
	<label for='msg-lock'><input type='checkbox' id='msg-lock' <?php echo ($Form['lock']? "checked value='1'":"value='0'"); ?> onclick="this.value = this.checked==true?1:0;"/>Закреплённое сообщение.</label><br>
	<label for='msg-broadcast'><input type='checkbox' id='msg-broadcast' <?php echo ($Form['broadcast']? "checked value='1'":"value='0'"); ?> onclick="this.value = this.checked==true?1:0;"/>Сообщение для всех.</label>
</fieldset>


<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','messageForm','<?php echo $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>
<script>
	ckEditorUsed =  'msg-text'; 
	CKEDITOR.replace(ckEditorUsed);
</script>