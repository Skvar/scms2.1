<?php
$imgWidth = (isset($Histogramm['width'])?$Histogramm['width']:800);
$imgHeight = (isset($Histogramm['height'])?$Histogramm['height']:200);
$period = (isset($Histogramm['period'])?$Histogramm['period']:10);
$type = (isset($Histogramm['type'])?$Histogramm['type']:0);
$dir = (isset($Histogramm['direction'])?$Histogramm['direction']:0);
$hName = "iHist".$GLOBALS['histnumber']++;

//looking for min and max values--------------------------------
$max = 0;
$min = 999999;
	foreach($Histogramm['data'] as $lineindex => $line){
		foreach($line as $posindex=>$val){
			if($val >= $max)	$max = $val;
			if($val <= $min)  	$min = $val;
		}
	}
//draw histogramm-----------------------------------------------
echo "<div style='width:".$imgWidth."px;margin:0 auto;'><canvas class='histogramm' width='".$imgWidth."' height='".$imgHeight."' id='$hName'></canvas></div>";
echo "<script>
		DrawHistogramm('$hName',$imgWidth,$imgHeight,$period,$min,$max,".json_encode($Histogramm['data']).",$type,$dir);
	</script>";
?>