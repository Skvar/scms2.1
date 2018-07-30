<br>
<?php

		
		$out['postheader'] = "Документы ".$build['name']."<hr>";
		$out['post'] = "Документы связанные c домом, ".$build['name'].".";		
		//tInsertContentHeader(Array('pagetitle'=>$pt));
		
		tInsertPost($out,'docblock',false);
					
		$Files = Array('filelist'=>'list','show'=>true,'type'=>2,'owner'=>$build['id'],'header'=>'Отчеты о выполнении договора управления.');			
		include("files.php");
		
		$Files = Array('filelist'=>'list','show'=>true,'type'=>8,'owner'=>$build['id'],'header'=>'Прочие документы.');			
		include("files.php");
		
		
?>
<br>