<?php
if(isset($_FILES["file-for-upload"])){
	$result = Array('result'=>false,'message'=>'','useform'=>false);
	$tmpname = $_FILES['file-for-upload']['tmp_name'];
	if ($_FILES["file-for-upload"]["error"] > 0) $result['message'] .= "Error uploading file";


	$fileupload =  $_FILES['file-for-upload']['name'];
	$flink = "../../../pub/".iconv( 'utf-8','cp1251',basename($fileupload));
	if(move_uploaded_file($tmpname,$flink)){
		$fname = $_FILES["file-for-upload"]["name"];
				
		$result['message'] = realpath($flink)." Успешно загружен. <br>".$tmpname;		
		$result['file'] = $fname;
		$result['link'] = $output_dir.$fname;
		$result['result'] = true; 
	}
	else{
		 $result['message'] .= ("Error moving file ".$_FILES['file-for-upload']['tmp_name']." => $flink");
	}
	
	
	echo json_encode($result);	
}
else{
?>

<form class='form-default-panel edit-file-panel' method='POST'  id='fileEditForm' name='fileEditForm' enctype='multipart/form-data' action='./templates/base/forms/EditFileForm.php'>

	<input type='hidden' id='file-owner' value='<?php echo $Form['file-owner']; ?>'>
	<input type='hidden' id='file-link' value=''>
	<div <?php echo ((isset($Form['file-id']) && $Form['file-id']>0) ? "style='display:none;'":"");  ?>>
	<div class='file-upload'>
		<label for='file-for-upload' class='file-upload-button'>Выбрать файл...</label>
		<input class='file-upload-input' type='file' name='file-for-upload' id='file-for-upload' onchange='fileSetName(this);'>
		<input type='text' class='file-name' readonly='true' id='file-name' value='<?php echo $Form['file-name']; ?>'>
	</div>
	<div id='file-progressbar' class='file-progressbar'><div id='file-bar' class='file-bar'></div></div>
	</div>

<div>
		<label for='file-description'>Описание файла:</label>
		<textarea id='file-description' style='height:100px;'><?php echo $Form['file-description']; ?></textarea>
</div>
<div>
		<table>
			<tr>
				<td>
					<label>Дата публикации:<input type='date' id='file-date' value='<?php echo $Form['file-date']; ?>'></label>	
				</td>
				<td>
					<label>Тип файла:<select id='file-type'><?php
						foreach($Form['file-types'] as $key=>$val){
							if($key == $Form['file-type']) echo "<option value='$key' selected>$val</option>";
							else						   echo "<option value='$key'>$val</option>";
						}	
					?></select></label>	
				</td>
			</tr>
		</table>		
</div>
<hr>
<button type='button' id='save-file-button' class='normal-button' onclick="WriteFormData('<?php echo $Form['handler']; ?>','fileEditForm','<?php echo $Form['arg']; ?>');" <?php echo ((isset($Form['file-id']) && $Form['file-id']>0) ? "":"disabled");  ?>>
	<div class='button-icon button-icon-ok'></div>
	Сохранить
</button>
<button type='button' class='normal-button' onclick="fileSaveCancel();">
	<div class='button-icon button-icon-back'></div>
	Закрыть
</button>
<script>

	$('#file-for-upload').on('click',function(){
			$('#fileEditForm').ajaxForm({
				beforeSend: function() {
				    $('#file-bar').width('0%');
				},
				uploadProgress: function(event, position, total, percentComplete){
				    $('#file-bar').width(percentComplete+'%');
				},
				success: function() {
				    $('#file-bar').width('100%');
				},
				complete: function(response) {
					//alert(response.responseText);
					try{
			        	obj = JSON.parse(response.responseText);
			        	if(obj['result'] == true){
			        		$('#file-link').val(obj['link']);  
			        		$('#save-file-button').prop('disabled',false);	
			        		console.log(obj['message']);      						
						}
						else{
							console.log(response.responseText);
						}
					}
					catch(CatchException){
						console.log(response.responseText);
					}
				},
				error: function(){
					alert("Ошибка загрузки");
				}
			});
	});
	
	function fileSetName(obj)
	{
		v = obj.files[0].name;
		$('#file-name').val(v);
		$('#file-description').val(v);
		
		$('#fileEditForm').submit();

	}
	
	function fileSaveCancel()
	{
		
		CloseForm();
	}
</script>
</form>

<?php
}
?>

