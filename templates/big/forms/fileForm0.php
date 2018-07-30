<?php 
/**************************************************************************************************
										BIG ICON FILES
***************************************************************************************************/
if(!isset($Files)) exit; 
?>
<div class='files-form'>
	<p onclick='ToggleOpenBlock("files-list<?php echo $Files['index']; ?>")'>Список приложенных файлов:</p>
	
	<div class='files-list files-list-largeicon<?php echo $Files['show'] ? ' files-list-open':''; ?>' id='files-list<?php echo $Files['index']; ?>'>
	<?php	
		$user = GetCurrentUser();
		$handler = "libs/files.php";
		foreach($Files['list'] as $key=>$val){
			
			
			

			echo "<div class='files-list-element files-list-element-largeicon' style='width:".$Files['width']."px;'>";
			if($user['right']>=100){
				tInsertSmallButtons("libs/files.php","&ajax=1&fid=".$val['id'],Array('edit'=>doEditFile,'delete'=>doDeleteFile));	
			}
			switch($val['type']){
				case fileImage:
					echo "<img id='".$val['filename']."' src='".$val['image']."' width='".$val['width']."' height='".$val['height']."' onclick='ShowImage(\"".$val['image']."\",".$val['fullwidth'].",".$val['fullheight'].");'>";
					echo "<p onclick='ShowImage(\"".$val['image']."\",".$val['fullwidth'].",".$val['fullheight'].");'>".$val['filedesc']."</p>";
				break;
				case fileDoc:
					echo "<img id='".$val['filename']."' src='".$val['image']."' width='".$val['width']."' height='".$val['height']."' onclick='ShowDoc(\"".$val['link']."\");'>";
					echo "<p  onclick='ShowDoc(\"".$val['link']."\");'>".$val['filedesc']."</p>";
				break;
				default:
					echo "<img id='".$val['filename']."' src='".$val['image']."' width='".$val['width']."' height='".$val['height']."'>";
					echo "<p>".$val['filedesc']."</p>";
				break;
			}	
			echo "</div>";					
		}
		
		if($user['right']>=100){
			$arg = "&ajax=1&fid=0&command=".doAddFile."&id=".$Files['owner']."&type=".$Files['type'];
			echo "<div class='files-list-element files-list-element-largeicon files-list-element-add' onclick='Commandsd(\"$handler\",\"$arg\");'>";
			echo "<img src='templates/base/images/icon_plus.png' width='30' height='30'>";
			echo "<p>Добавить файл.</p>";
			echo "</div>";
		}
	?>	
	</div>	
</div>