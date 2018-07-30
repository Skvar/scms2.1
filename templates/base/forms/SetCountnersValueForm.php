 <?php 
 setlocale(LC_ALL, 'en_GB');
 ?>
 <form method='POST' oninput="onCalcForm();" id='countForm'>
	 <div class='input-panel'>
		 <div class='input-panel-header'>
		 Ввод показаний индивидуальных приборов учета
		 </div>
		
		 <table>
			 <tr>
				 <th  width='25%'>Счетчик</th>
				 <th  width='15%'>Предыдущие показания<br><?= $Counters[0]['values'][0]['date']; ?></th>
				 <th  width='15%'>Текущие показания<br><?= date("d.m.Y"); ?></th>
				 <th  width='15%'>Расход</th>
				 <th  width='15%'>Тариф</th>
				 <th  width='15%'>Сумма</th>
			 </tr>
			<?php
				$oldsrv = 0;
				$cnt = 1;
				$tabindex = 1;
				//echo count($Counters);
				foreach($Form['counters'] as $key => $count){
					$srvnum = intval(isset($count['service']) ? $count['service'] : 0);
					if($oldsrv != $srvnum){  
						$cnt=1;
						$oldsrv	=	$srvnum;
					}		
					echo "<tr>";
						echo "<td>";
							echo "<span class='small-text'>".(isset($count['name']) ? $count['name'] : "").", <b>счетчик № ".($cnt++)."</b></span>";
						echo "</td>";
						
						$rate = 0;
						$cost = 0;
						if(isset($count['id']) && $count['id']){
							if(count($count['values'])<2){
								$count['values'][1]['value']=$count['values'][0]['value']; 	
							}
							$rate = $count['values'][1]['value'] - $count['values'][0]['value'];
							$cost = $rate *$count['price']; 
							echo "<td>";
						 		echo "<input type='text' tabindex='-1' name='old".$count['id']."' id='old".$count['id']."' readonly value='".$count['values'][0]['value']."'>";
					 		echo "</td>";	 		
					 		echo "<td>";
								echo "<input type='number' min='".intval($count['values'][0]['value'])."' tabindex='$tabindex' step='0.01' 
										name='cur".$count['id']."' id='cur".$count['id']."' required onclick='this.select();'  
										oninput='onInput(this,".$count['id'].")'  value='".$count['values'][1]['value']."' ".($Form['enabled']?"":"disabled").">";
							echo "</td>";
						}
						else{
							echo "<td colspan='2'></td>";
						}
						$cid = isset($count['id']) ? $count['id'] : 0;
						echo "<td>";
							echo "<input type='text' tabindex='-1' name='rate".$cid."' id='rate".$cid."' readonly  value='".number_format($rate,2,".","")."'>";
						echo "</td>";
						echo "<td>";
							 echo "<input type='text' tabindex='-1' name='price".$cid."' id='price".$cid."' readonly  value='".(isset($count['price']) ? $count['price'] : 0)."'>";
						echo "</td>";
						echo "<td>";
							 echo "<input type='text' tabindex='-1' name='cost".$cid."' id='cost".$cid."' readonly  value='".number_format($cost,2,".","")."'>";
						echo "</td>";
						$tabindex++;
				}
	
			?>
		 </table>
		 <div class='input-panel-footer'>
		 <button type='button' <?php  echo $Form['enabled']?"":"disabled"; ?> id='bSave' class='normal-button button-blue' onclick="writeCountersValue();" tabindex='<?php echo $tabindex;?> '>
		 <div class='button-icon button-icon-ok'></div>
		 Отправить
		 </button>
		 <button type='button'  <?php  echo $Form['enabled']?"":"disabled"; ?> class='normal-button' onclick="ResetForm();">
		 <div class='button-icon button-icon-reset'></div>
		 Сброс
		 </button>
		 </div>
	 </div>
 </form>
 

<script>

function writeCountersValue()
{
//Сбор информации----------------------------------------------------------
	childs =  document.getElementsByTagName('input');
	var vals = 0;
	
	ClosePopups();
	
	
	var data = new Object();
	
	for(var a=0;a<childs.length;a++){
		var sid = childs[a].id;
		if(sid.substr(0,3) == "cur"){
			id = Number(sid.substr(3,sid.lenght)).toFixed(0);
			val = Number(childs[a].value).toFixed(2);	

			data[id] = val;
		}
	}
	datastr = JSON.stringify(data);

	
	SendRequest('<?= $Form['handler']; ?>','ajax=1<?= $Form['arg']; ?>&data='+datastr,function(req){
		if(req.responseText.length > 0){
			try{
	        	 obj = JSON.parse(req.responseText);
	        	 if(obj['result'] == true){	
	        	 	out  = document.getElementById('<?= $Form['out']; ?>');
	        	 	if(out){					
						out.innerHTML = obj['message'];
					}
	        	 		   	
					return true;		
				} 
	       	}    
	        catch (CatchException){
	        	 console.log(req.responseText);
	        }						
		}
	});	
}

/*************************************************************************************
				проверка ввода и подсчет строки
**************************************************************************************/
function onInput(inp,id) {

  ClosePopups();
  
  tarif = document.getElementById('price' + id).value;
  oval = document.getElementById('old' + id).value;
  cval = inp.value;
  rate = Number(cval - oval).toFixed(2);
  cost = Number(rate * tarif).toFixed(2);
  err = false;
  
  var maxResource = <?= coreSettings('maxResourceValue'); ?>;
  
  
  if(rate > maxResource){
  	 PopupMessage(inp,"Расход превышает максимально допустимый ("+ rate +"m<sup>3</sup> > " + maxResource + "m<sup>3</sup>), проверьте введенные данные.");	 
  }
  if(rate < 0 ){
  	 PopupMessage(inp,"Расход меньше нуля, проверьте введенные данные.");
  }
  
  document.getElementById('rate' + id).value = (rate);
  document.getElementById('cost' + id).value = (cost); 
}

function onCalcForm()
{

	err=false;
	var childs =  document.getElementsByTagName('input');
	var vals = 0;
	var maxResource = <?= coreSettings('maxResourceValue'); ?>;
	
	for(var a=0;a<childs.length;a++){
		var id = childs[a].id;
		if(id!="rate0"){
			if(id.substr(0,4) == "rate"){
				val = Number(childs[a].value).toFixed(2);
				
				if(val > maxResource)err=true;
  				if(val < 0 )err = true;		
				vals += Number(val);	
			}
		}
		else{
			tarif = document.getElementById('price0').value;
			document.getElementById('rate0').value = Number(vals).toFixed(2);
			document.getElementById('cost0').value = Number(vals*tarif).toFixed(2);
		}
	}
	
if(err){
  	$('#bSave').prop('disabled',true);
  }
else{
  	$('#bSave').prop('disabled',false);	
}
	
}

function ResetForm()
{
	document.getElementById("countForm").reset();
	onCalcForm();
}

</script>











