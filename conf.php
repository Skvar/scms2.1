<?php
$GLOBALS['histnumber'] = 0;

if(!defined("confOurOrg"))					define("confOurOrg",473);
if(!defined("confTheme"))					define("confTheme",'base');
if(!defined("workDir"))						define("workDir",'/');
if(!defined("outputDir"))					define("outputDir",'/pub'); 




set_include_path($_SERVER['DOCUMENT_ROOT'].workDir."/".PATH_SEPARATOR.
				 $_SERVER['DOCUMENT_ROOT'].workDir."/libs/".PATH_SEPARATOR.
				 $_SERVER['DOCUMENT_ROOT'].workDir."/templates/".confTheme."/".PATH_SEPARATOR.
				 $_SERVER['DOCUMENT_ROOT'].workDir."/templates/".confTheme."/forms/".PATH_SEPARATOR.
				 $_SERVER['DOCUMENT_ROOT'].workDir."/modules/");

?>