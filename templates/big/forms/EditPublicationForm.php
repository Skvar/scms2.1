<form class='input-panel publication-panel' method='POST'  id='publicationForm'>
<div>
		<label for='pub-header'>Заголовок:</label>
		<input type='text'  id='pub-header' value='<?php echo $Form['pubheader']; ?>'>
</div>

<?php if($Form['type']>1){

	echo "<div>
			<label for='pub-description'>".($Form['type']==pubLinks ? "Ссылка:" : "Краткое описание:")."</label>
			<input type='text'  id='pub-description' value='".$Form['pubdescription']."'>
	</div>";
}
?>
<div>

		<label for='pub-text'>Текст поста:</label>
		<textarea id='pub-text' style='height:100px;'><?php echo $Form['text']; ?></textarea>
</div>
<div>

		<label>Дата публикации:<input type='date' id='pub-date' value='<?php echo $Form['pubdate']; ?>'></label>
</div>
<hr>
<button type='button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','publicationForm','<?php echo $Form['arg']; ?>');">
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="CloseForm();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
</form>
<script>
	ckEditorUsed =  'pub-text'; 
	CKEDITOR.replace(ckEditorUsed);
</script>