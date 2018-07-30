<?php
if(isset($_FILES['fileUpload'])){
	echo "<br>";
	tInsertContentHeader(Array('pagetitle'=>"Результат обработки архива ".basename($_FILES['fileUpload']['name'])));
	
	$fname = "pub/".basename($_FILES['fileUpload']['name']);
	copy($_FILES['fileUpload']['tmp_name'],$fname);
	$zip = new ZipArchive();
 
	$zip->open($fname);
	
	echo "<br>";
	
	$zip->extractTo("pub/tmp/");
	$zip->close();
	unset($zip);
	
	$filelist = glob("pub/tmp/*.csv");
	
    foreach ($filelist as $filename){
    	$res = Array('message'=>'','result'=>false);
    	$bname = substr(basename($filename),0,3);
    	switch($bname){
			case "deb"://Дебиторы------------------------
				$res = loadBuildDebitors($filename);
			break;
			case "cnt"://Счетчики------------------------
				$res = loadCounterObjects($filename);
			break;
			case "val"://Показания счетчиков-------------
				$res = loadCounterValues($filename);
			break;
			case "acc"://Показания счетчиков-------------
				$res = loadAccounts($filename);
			break;
			case "bld"://Адресный фонд-------------------
				$res = loadBuilding($filename);
			break;
			case "res"://Расход ресурсов-------------------
				$res = loadResources($filename);
			break;
		}
		
		$out['postheader'] = "Обработка ".$filename;
		$out['post'] = "Размер: ".filesize($filename)." байт <br>".$res['message']."<br>";
    	
 	
    	tInsertPost($out,'',false);

    }	
    
    $out['postheader'] = "Очистка временной папки ";
    $filelist = glob("pub/tmp/*.*");
    $c = count($filelist);
    
    $out['post'] = "Количество файлов: ".$c."<br>";
    
    if (count($filelist) > 0) {
        foreach ($filelist as $file) {      
            if (file_exists($file)) {
            unlink($file);
            }   
        }
    }
    $out['post'] .="Успешно.";
    tInsertPost($out,'',false);
}
else{
?>
<br>
<form method='POST' class='input-panel adm1-panel' action='<?php makeLink(-1,-1,-1,-1,-1,-1); ?>'  enctype='multipart/form-data'>
		 <fieldset>
		 	<legend>Синхронизация данных</legend> 
		 	<input type='file' name = 'fileUpload' accept='.zip,.7z'/>
		 	<hr>
			 <div>
				 <button type='submit' class='normal-button'>
				 	<div class='button-icon button-icon-ok'></div>
				 	Отправить
				 </button>
				 <button type='reset' class='normal-button'>
				 	<div class='button-icon button-icon-reset'></div>
				 	Сброс
				 </button>
			 </div>
		 </fieldset>

 </form>

<?php
}




/********************************************************************************************************
										 Ресурсы
*********************************************************************************************************/
function loadResources($filename)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$query = "DELETE FROM livComResource";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	
	$query = "LOAD DATA LOCAL INFILE '$filename' REPLACE
				INTO TABLE livComResource
				CHARACTER SET cp1251
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
				
	if($dbptr->Query($query)){
	}
	else{
		 return Array('message'=>' ошибка загрузки','result'=>false);
	}

}

/********************************************************************************************************
										 Адреса
*********************************************************************************************************/
function loadBuilding($filename)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$query = "DELETE FROM livBuilding";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	
	$query = "LOAD DATA LOCAL INFILE '$filename' REPLACE
				INTO TABLE livBuilding
				CHARACTER SET cp1251
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
				
	if($dbptr->Query($query)){
	}
	else{
		 return Array('message'=>' ошибка загрузки','result'=>false);
	}

}
/********************************************************************************************************
										 Лицевые
*********************************************************************************************************/
function loadAccounts($filename)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$query = "DELETE FROM livComAccounts";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	
	$query = "LOAD DATA LOCAL INFILE '$filename' REPLACE
				INTO TABLE livComAccounts
				CHARACTER SET cp1251
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
				
	$query1 = "	UPDATE sysUserAuth
				JOIN livComAccounts acc on acc.id = account
				SET login = LEFT(employer,LOCATE(' ',employer)),username = employer
				WHERE username<>acc.employer AND userright < 100";
		
	if($dbptr->Query($query)){
//update user names and logins---------------------------------------------------------------------	
		if($dbptr->Query($query1)) return Array('message'=>'Успешно загружено','result'=>true);
	}
	else{
		 return Array('message'=>' ошибка загрузки','result'=>false);
	}

}
/********************************************************************************************************
										 СЧЕТЧИКИ
*********************************************************************************************************/
function loadCounterObjects($filename)
{
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$query = "LOAD DATA LOCAL INFILE '$filename' REPLACE
				INTO TABLE livCounters
				CHARACTER SET utf8
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
		
	if($dbptr->Query($query)){
			return Array('message'=>'Успешно загружено','result'=>true);
	}
	else{
		 return Array('message'=>' ошибка загрузки','result'=>false);
	}
}


/********************************************************************************************************
										ПОКАЗАНИЯ СЧЕТЧИКОВ
*********************************************************************************************************/
function loadCounterValues($file)
{
	$countStats = isset($GLOBALS['countstats']) ? $GLOBALS['countstats'] : Array('enabled'=>false,'arc_loaded'=>false,'arc_cleared'=>false);	
	
	
	if(!$countStats['arc_loaded']) return Array('message'=>' Ошибка загрузки. Предыдущие показания не были сохранены','result'=>false);
	
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	$query = "DELETE FROM livCountersValue";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	
	
	$loadfile = $file.".txt";
	$FILE = fopen($file,'rt');
	$OUT = fopen($loadfile,'wt');
	
	
	while (!feof($FILE)){
		$str = fgets($FILE);
		$vals = explode(';',$str);
		if(count($vals)>=5){
			$newstr = ";".$vals[1].";".$vals[2].";".$vals[3].";".$vals[4].";".$vals[5].";\n";
			
			$res = fwrite($OUT,$newstr);
		}
		
	}
	
	fclose($OUT);
	fclose($FILE);
	
	$query = "LOAD DATA LOCAL INFILE '$loadfile' REPLACE
				INTO TABLE livCountersValue
				CHARACTER SET utf8
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
		
	if($dbptr->Query($query)){
			return Array('message'=>'Успешно загружено','result'=>true);
	}
	else{
		 return Array('message'=>' ошибка загрузки','result'=>false);
	}
}
/********************************************************************************************************
										ЗАДОЛЖЕННОСТЬ ПО ДОМАМ
*********************************************************************************************************/
function loadBuildDebitors($file)
{
	
	
	$loadfile1 = $file."saldo.txt";
	$loadfile2 = $file."accrual.txt";
	$loadfile3 = $file."recalc.txt";
	$loadfile4 = $file."pay.txt";
	$loadfile5 = $file."builddeb.txt";
	
	$dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) return false;
	
	
	$query = "DELETE FROM livComSaldo";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	$query = "DELETE FROM livComAccrual";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	$query = "DELETE FROM livComRecalc";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	$query = "DELETE FROM livComPay";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	$query = "DELETE FROM livBuildDebitors";
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка очистки','result'=>false);
	
	
	
	$FILE = fopen($file,'rt');
	
	$OUT1 = fopen($loadfile1,'wt');
	$OUT2 = fopen($loadfile2,'wt');
	$OUT3 = fopen($loadfile3,'wt');
	$OUT4 = fopen($loadfile4,'wt');
	$OUT5 = fopen($loadfile5,'wt');
	
	
	
	while (!feof($FILE)){
		$str = fgets($FILE);
		$vals = explode(';',$str);
		if(count($vals)>=8){
			if($vals[4]==0){
				$newstr1 = ";".$vals[2].";".$vals[4].";".$vals[5].";".$vals[3]."\n";
				fwrite($OUT1,$newstr1);
				if(isset($bdeb[$vals[1]][$vals[3]])) 	$bdeb[$vals[1]][$vals[3]] += $vals[5];
				else									$bdeb[$vals[1]][$vals[3]] = $vals[5];		
			}
			else{
				if($vals[5]!=0){
					$newstr1 = ";".$vals[2].";".$vals[4].";".$vals[5].";".$vals[3]."\n";
					fwrite($OUT1,$newstr1);
				}
				if($vals[6]!=0){
					$newstr2 = ";".$vals[2].";".$vals[4].";".$vals[6]."\n";
					fwrite($OUT2,$newstr2);
				}
				if($vals[7]!=0){
					$newstr3 = ";".$vals[2].";".$vals[4].";".$vals[7]."\n";
					fwrite($OUT3,$newstr3);
				}
				if($vals[8]!=0){
					$newstr4 = ";".$vals[2].";".$vals[4].";".$vals[8]."\n";
					fwrite($OUT4,$newstr4);
				}			
			}		
		}		
	}
	
	foreach($bdeb as $bkey => $bval){
		foreach($bval as $pkey => $pval){
			$str = ";".$pkey.";0;".$pval.";".$bkey.";\n";;	
			fwrite($OUT5,$str);
		}
	}
	fclose($OUT1);
	fclose($OUT2);
	fclose($OUT3);
	fclose($OUT4);
	fclose($OUT5);
	fclose($FILE);
	
	$query = "LOAD DATA LOCAL INFILE '$loadfile5' REPLACE
				INTO TABLE livBuildDebitors
				CHARACTER SET utf8
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
	
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка загрузки','result'=>false);
	
	
	$query = "LOAD DATA LOCAL INFILE '$loadfile1' REPLACE
				INTO TABLE livComSaldo
				CHARACTER SET utf8
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
	
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка загрузки','result'=>false);
	
	$query = "LOAD DATA LOCAL INFILE '$loadfile2' REPLACE
				INTO TABLE livComAccrual
				CHARACTER SET utf8
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
	
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка загрузки','result'=>false);
	
	$query = "LOAD DATA LOCAL INFILE '$loadfile3' REPLACE
				INTO TABLE livComRecalc
				CHARACTER SET utf8
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
	
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка загрузки','result'=>false);
	
	$query = "LOAD DATA LOCAL INFILE '$loadfile4' REPLACE
				INTO TABLE livComPay
				CHARACTER SET utf8
				FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n'";
	
	if(!$dbptr->Query($query)) return Array('message'=>' ошибка загрузки','result'=>false);

	return Array('message'=>'Успешно загружено','result'=>true);
	
}
?>