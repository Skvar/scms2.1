<?php
/****************************************************************************************
							ПОДКЛЮЧЕНИЕ К БД
****************************************************************************************/
class CMYSQL extends mysqli
{
	public $Host;
	public $Name;
	public $LastRecord;
	
	private $Context;
	private $Handle;
	
//----------------------------------------------------------------------------------	
	function CMYSQL($conffile)
	{
		require($conffile);
		$this->Host = $db_host;
		$this->Name = $db_name;
		
		$this->Context = mysqli_init();
		
		mysqli_options($this->Context,MYSQLI_OPT_LOCAL_INFILE, true);
		
		if (!mysqli_real_connect($this->Context,$this->Host, $db_user, $db_pass,$this->Name)) {
    		trigger_error("CMYSQL:Error connect db",E_USER_ERROR);
    		exit();
		}
 		/*$res = mysqli_select_db($this->Handle,$this->Name);
		if (!$res) {
    		trigger_error("CMYSQL:Error select db".$this->Name,E_USER_ERROR);
    		exit();
		}*/
 		
 		mysqli_query ($this->Context,"set character_set_client='utf8'"); 
 		mysqli_query ($this->Context,"set character_set_results='utf8'"); 
 		mysqli_query ($this->Context,"set collation_connection='utf8_general_ci'");
 		
 		
 			
		unset($db_user,$db_pass,$db_name,$db_host);		
	}
//-----------------------------------------------------------------------------------	
	function Query($query)
	{
		$res = mysqli_query($this->Context,$query);
		echo mysqli_error($this->Context);	
		if($res!==false){
			$this->LastRecord = mysqli_insert_id($this->Context);
			return true;
		}
		return false;
	}
//-----------------------------------------------------------------------------------
//Загрузка в массив------------------------------------------------------------------------
	function LoadData($query,$flat=false,$name="",$value="")
	{	
		$out = Array();
		$res = mysqli_query($this->Context,$query);
		if (mysqli_connect_errno()) {
    		printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    		exit();
		}
		if($res === false){
			//trigger_error("LoadData:Error in query: ".$query."<br>The query didn't return answer",E_USER_WARNING);
			return false;
		}
		while($data = mysqli_fetch_assoc($res)){
			$out[] = $data;	
		}
			if (error_get_last()!=NULL){
				//echo $query;
				//exit();
				//return false;
			}
		
		if($flat){
			$fout = Array();
			for($a=0;$a<count($out);$a++) $fout[$out[$a][$name]] = $out[$a][$value];
			return $fout;
		}
		else	return $out;
	}

//Загрузка строки в массив------------------------------------------------------------------------
	function LoadRow($query)
	{	
		$qu = str_replace("SELECT","SELECT TOP 1",$query);
		
		$res = mysqli_query($this->Context,$query);
		if (mysqli_connect_errno()) {
    		printf("Не удалось подключиться: %s\n", mysqli_connect_error())." ".$query."<br>";
    		exit();
		}
		if($res === false){
			//trigger_error("LoadRow: Error in query: ".$query."<br>The query didn't return answer",E_USER_WARNING);
			return false;
		}
		$ix = 0;
		$data = mysqli_fetch_assoc($res);
		if (error_get_last()!=NULL){
			//echo "Error in query: ".$query;
			return false;
		}
		
		if(empty($data))return false;
		
		
		return $data;
	}

		
};
?>