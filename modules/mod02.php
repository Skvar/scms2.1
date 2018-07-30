<?php
if(!defined("tabCounters"))				define("tabCounters",0);
if(!defined("tabLoadDB"))				define("tabLoadDB",1);
if(!defined("tabSettings"))				define("tabSettings",2);
if(!defined("tabUsers"))				define("tabUsers",3);
if(!defined("tabMessage"))				define("tabMessage",4);
if(!defined("tabVisited"))				define("tabVisited",5);
if(!defined("tabMaintenance"))			define("tabMaintenance",6);


iInsertPages(Array('Счетчики'=>'','Обслуживание БД'=>'','Настройки'=>'','Пользователи'=>'','Сообщения'=>'','Посещаемость'=>'','Текущий ремонт'=>''),$tab);



switch($tab){
	
	case tabCounters:
		include("mod020.php");
	break;
	case tabSettings:
		include("mod022.php");
	break;
	case tabLoadDB:
		include("mod021.php");
	break;
	case tabUsers:
		include("mod023.php");
	break;
	case tabMessage:
		include("mod024.php");
	break;
	case tabVisited:
		include("mod025.php");
	break;
		case tabMaintenance:
		include("mod026.php");
	break;
}







?>