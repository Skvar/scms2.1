<?php
if(!defined("fileCommon"))				define("fileCommon",0);
if(!defined("fileImage"))				define("fileImage",1);
if(!defined("fileDoc"))					define("fileDoc",2);
//AJAX обработчик---------------------------------------------------------------------------------------------
if(isset($_REQUEST['ajax'])){
	include("../ajax.php");
	$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : doNope;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$fid = isset($_REQUEST['fid']) ? $_REQUEST['fid'] : 0;
	$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : "";
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 0;
	

	$user = GetCurrentUser();
	$accountID = $user['account'];
	$date = date("Y-m-d");
	
	$result = Array('result'=>false,'message'=>'','useform'=>false);
	
	$output_dir = "pub/";
	


	switch($command){
		case doEditFile:
			$file=$dbptr->LoadRow("SELECT id,filedesc as description, type as filetype,link as filefullpath  FROM livFiles WHERE id=$fid");	
		case doAddFile:
			$Form['file-id'] 			= $fid;
			$Form['file-date'] 			= isset($file['filedate'])		? $file['filedate'] 	: date("Y-m-d");
			$Form['file-description'] 	= isset($file['description'])	? $file['description'] 	: "";
			$Form['file-name'] 			= isset($file['filename'])	? $file['filename'] 	: "";
			$Form['file-fullpath'] 		= isset($file['filefullpath'])	? $file['filefullpath'] 	: "";
			$Form['file-owner'] 		= $id;
			$Form['file-type'] 			= isset($file['filetype'])	? $file['filetype'] 	: $type;
			
			$Form['file-types'] = coreLoadProperty("fileTypes");
					
	

			$Form['handler'] 	= "libs/files.php";
			$Form['arg'] 		= "&command=".doSave."&fid=".$fid;
			$result['message'] 	= corePutForm("EditFileForm.php",$Form);
			$result['header'] 	= $fid ? ("Редактирование файла:".$Form['file-name']) : "Добавление нового файла";
			$result['headericon'] = 'templates/base/images/icon_buildmnt.png';
			$result['user'] 	= $user['name'];
			$result['useform'] 	= true;	
			$result['result'] 	= true;
		break;
		case doSave:

			$vals = json_decode($data,true);
			
			$fileowner = $vals['file-owner'];
			$filename = $vals['file-link'];
			$filelink = $output_dir.$vals['file-link'];
			$filedate = $vals['file-date'];
			$filetype = $vals['file-type'];
			$filedescription = $vals['file-description'];
			
			if($fid){
				$query = "UPDATE livFiles SET filedesc='$filedescription', type=$filetype WHERE id=$fid";
			}
			else{
				$query = "INSERT livFiles (owner,filename,filedesc,link,type,flag) VALUES($fileowner,'$filename','$filedescription','$filelink',$filetype,0);";
			}
			
			if($dbptr->Query($query)){
				$result['message'] = "";//Сохранено успешно.";////print_r($vals,true);
				$result['reload'] = true;
				$result['result'] = true;
						
			}
			else{
				$result['reload'] = false;
				$result['message'] = "Ошибка сохранения<br>".$query;
			}			
		break;
		case doDeleteFile:
			$file=$dbptr->LoadRow("SELECT link as filefullpath  FROM livFiles WHERE id=$fid");	

			if(file_exists( $file['filefullpath'] )) unlink("../../".$file['filefullpath']);
				if($dbptr->Query("DELETE FROM livFiles WHERE id=$fid")){
					$result['message'] .= "";//Сохранено успешно.";////print_r($vals,true);
					$result['reload'] = true;
					$result['result'] = true;
				}
				else{
					 $result['reload'] = false;
					$result['message'] = "Ошибка сохранения<br>";
				}		
				
		break;
	}
	unset($_REQUEST['ajax']);
	unset($_REQUEST['command']);
	unset($_REQUEST['data']);
	
	echo json_encode($result);
	
}
else{
	if(!isset($dbptr)) $dbptr = $GLOBALS['dbptr'];
	if(!class_exists('CMYSQL')) exit;
	
	$query = "SELECT * FROM livFiles files WHERE type=".$Files['type']." AND owner=".$Files['owner'];
	if(!isset($Files['type'])) $Files['type'] = 0;
	
	$Files['list'] = $dbptr->LoadData($query);
	
	if(!isset($Files['width'])) $Files['width'] = 30;
	if(!isset($Files['height'])) $Files['height'] = 30;
	
	
	foreach($Files['list'] as $key => $file){
		if(file_exists( $file['link'] )){
			$fstats = stat ( $file['link'] );
			$type = mime_content_type ( $file['link'] );
			$tt = explode('/',$type);
			
			$Files['list'][$key]['size'] = $fstats['size'];
			$Files['list'][$key]['date'] = $fstats['mtime'];
			
			switch($tt[0]){
				case "image":	
					$Files['list'][$key]['type'] = fileImage;
					$Files['list'][$key]['image'] = $file['link'];
					
				break;
				case "application":
					$Files['list'][$key]['type'] = fileDoc;
					$Files['list'][$key]['image'] = "templates/base/images/icon_docs.png";	
				break;
				case "text":
					$Files['list'][$key]['type'] = fileDoc;
					$Files['list'][$key]['image'] = "templates/base/images/icon_docs.png";	
				break;
			}	
		}
		else{
			 $Files['list'][$key]['type'] = fileCommon;
			 $Files['list'][$key]['image'] = "templates/base/images/icon_nofile.png";
		}
		
			$info = getimagesize( $Files['list'][$key]['image'] );
			$imgWidth = $info[0];
			$imgHeight = $info[1];
					
			$iw = $imgWidth/$Files['width'];
			$ih = $imgHeight/$Files['height'];
					
			$k = max($iw,$ih);
			
			$Files['list'][$key]['fullwidth'] = $imgWidth;
			$Files['list'][$key]['fullheight'] =  $imgHeight;
					
			$Files['list'][$key]['width'] = $imgWidth/$k;
			$Files['list'][$key]['height'] =  $imgHeight/$k;
			
			
	}

if(isset($GLOBALS['files']['index'])) $GLOBALS['files']['index']++;
else $GLOBALS['files']['index'] = 0;

$Files['index'] = $GLOBALS['files']['index'];
switch($Files['filelist']){
	case "largeicon":
		include("fileForm0.php");
	break;
	case "list":
		include("fileForm1.php");
	break;
}

?>

<script>
	function filesAddNew()
	{
	}
</script>

<?php
}
?>




