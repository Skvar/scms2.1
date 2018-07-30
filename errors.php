<?php
ini_set('display_errors',1);
ini_set('error_reporting',-1);

//---------------------------------------------------------
$old_error_handler = set_error_handler("customErrorHandler");

function PutErrorString($mess)
{
	echo "<div class='low_message low_error'>$mess</div>";
}
//---------------------------------------------------------
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
switch ($errno){
    case E_USER_ERROR:
        echo "<div class='low_message low_error'><b>USER ERROR [$errno]</b><br><span>$errstr<br>Error on line $errline in file $errfile;<br>PHP ".PHP_VERSION."(".PHP_OS.")<br>;Aborting...<br></span></div>";
        break;
    case E_USER_WARNING:
        echo "<div class='low_message low_warning'><b>USER WARNING [$errno]</b><br><span>$errstr<br></span></div>";
        break;

    case E_USER_NOTICE:
        echo "<div class='low_message low_notice'><b>USER NOTICE [$errno]</b><br><span>$errstr<br></span></div>";
        break;

    default:
        echo "<div class='low_message low_error'><b>SYSTEM ERROR: [$errno]</b><br><span>$errstr<br>in file: $errfile,in string: $errline<br></span></div>";
        break;
}
	echo "<hr>";
	
	return true;
}

//---------------------------------------------------------
?>