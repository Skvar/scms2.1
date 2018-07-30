<?php 
/**************************************************************************************************
										SMALL ICON FILES
***************************************************************************************************/
if(!isset($Files)) exit; 
?>
<div class='files-form'> 
	<div class='image-dummy' id='image-dummy' onclick='ShowImage("",0,0)'></div>
	<p onclick='ToggleOpenBlock("files-list<?= $Files['index']; ?>")'><?= isset($Files['header']) ? $Files['header']: "Список приложенных файлов:"; ?></p>
	<div class='files-list files-list-list<?= $Files['show'] ? ' files-list-open':''; ?>' id='files-list<?= $Files['index']; ?>'>
	<?php
		$user = GetCurrentUser();
		foreach($Files['list'] as $key=>$val){		
			if($user['right']>=100){ 
				tInsertSmallButtons("libs/files.php","&ajax=1&fid=".$val['id'],Array('edit'=>doEditFile,'delete'=>doDeleteFile));
			}
	
			echo "<div class='files-list-element files-list-element-list'>";
			switch($val['type']){
				case fileImage:
					echo "<img id='".$val['filename']."' src='".$val['image']."' width='".$val['width']."' height='".$val['height']."' onclick='ShowImage(\"".$val['image']."\",".$val['fullwidth'].",".$val['fullheight'].");'>";
					echo "<p onclick='ShowImage(\"".$val['image']."\",".$val['fullwidth'].",".$val['fullheight'].");'>".$val['filedesc']."</p>";
				break;
				case fileDoc:
					echo "<img id='".$val['filename']."' src='".$val['image']."' width='".$val['width']."' height='".$val['height']."' onclick='ShowDoc(\"".$val['link']."\");'>";
					echo "<p  onclick='ShowDoc(\"".$val['link']."\");'>".$val['filedesc']."</p>";
					echo "&nbsp;&nbsp;<span style='font:10px Arial;'>(Добавлен ".date("d.m.Y",$val['date']).")</span>";
					echo "<a href='https://ваш-управдом-плюс.рф/".$val['link']."' target='_blank'><i style='margin-right:30px;'>(Скачать)</i></a>";
				break;
				default:
					echo "<img id='".$val['filename']."' src='".$val['image']."' width='".$val['width']."' height='".$val['height']."'>";
					echo "<p>".$val['filedesc']."</p>";
				break;
			}		
			echo "</div>";
		}	
		
		if($user['right']>=100){		
			echo "<hr><div class='files-list-element files-list-element-list'>
			 <img src='templates/base/images/icon_plus.png' width='30' height='30' onclick='Commandsd(\"files.php\",\"&ajax=1&fid=0&command=".doAddFile."&id=".$Files['owner']."&type=".$Files['type']."\");'>
			 <p onclick='Commandsd(\"libs/files.php\",\"&ajax=1&fid=0&command=".doAddFile."&id=".$Files['owner']."&type=".$Files['type']."\");'>Добавить файл.</p></div>";
		}
	?>

		
	</div>	
</div>