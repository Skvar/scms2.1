<?php
$agent = $_SERVER['HTTP_USER_AGENT'];
preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info); // регулярное выражение, которое позволяет отпределить 90% браузеров


if(isset($browser_info['1']) && isset($browser_info['2'])){
	 if($browser_info['1'] == 'MSIE' && floatval($browser_info['2']) < 9) include("invalbrowser.php");
}

?>