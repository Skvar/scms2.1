<br>
<?php

		$out['postheader'] = "Тарифы.<hr>";
		$out['post'] = "Тарифы на текущий период для ".$build['name'];		
		//tInsertContentHeader(Array('pagetitle'=>$pt));
		
		tInsertPost($out,'tarifblock',false);


$prop = $build['property'];
$Parametrs = Array('command'=>doList,'allsquare'=>$prop['Площадь']+$prop['Площадь нежилых помещений'],'sharesuqare'=>$prop['Площадь общедомового имущества'],'owner'=>$id);

include("mod22.php");

unset($Parametrs);
?>