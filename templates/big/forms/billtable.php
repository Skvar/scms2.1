<style>
table{border-collapse: collapse;border-spacing: 0;display: inline-table;border-color:#A0A0A0;}
.billtable-main-layout{text-align: center;margin:5px;}
.billtable{display: inline-block;width:900px;padding:20px;}
.billtable h1{font-size: 20px;text-align: center;}
.billtable-table-layout{text-align: center;}
.billtable-table{display: inline-table;width:100%;font:10pt 'Arial';padding:0px;}					
.billtable-table th{background-color: #b1f7fe;}
.billtable-table td{text-align: left;padding:1px 3px;}
.billtable-table td:first-child{text-align: right;}
.billtable-foot-layout{width:400px;display: inline-block;}
.billtable-layout{display: inline-block;}
.billtable-qr{width:150px;height:150px;border:1px solid silver;}
.billtable-info1{vertical-align: top;text-align: center;}
.billtable-info{width:600px;text-align: left;}
.billtable-info p{margin: 5px 0px;font-size: 16px;font-weight: bold;}
</style>


<div class='billtable-main-layout'>
<div class='billtable'>
	<div class='billtable-layout  billtable-header'>
		<h1>ВЫПИСКА ЛИЦЕВОГО СЧЕТА за <?php setlocale(LC_TIME, 'ru_RU.UTF8'); echo strftime("%B %Y",strtotime($Form['period']));  ?> </php></h1>
		<div class='billtable-layout billtable-info'>
			<p>Информация о собственнике:</p>
			<p>ФИО:<?php echo $Form['account']['employer'];?>;&nbsp;&nbsp;ЛС:&nbsp;<?php echo $Form['account']['accNumber'];?></p>
			<p>Адрес:<?php echo $Form['account']['location'];?></p>
			<p>Общая площадь:&nbsp;<?php echo $Form['account']['square'];?>м<sup>2</sup>;&nbsp;&nbsp;Зарегестрированно:&nbsp;<?php echo $Form['account']['registered'];?>чел.</p>
		</div>
		<div class='billtable-layout billtable-info1'>
			<p>Дата распечатки:</p>
			<p><?php echo date('d.m.Y');?></p>
		</div>
	</div>
	<div class='billtable-layout billtable-qr'>
		<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $Form['account']['qr']; ?>" alt="" />
	</div>
	<div class='billtable-table-layout'>
		<p>РАСЧЕТ РАЗМЕРА ПЛАТЫ ЗА СОДЕРЖАНИЕ И РЕМОНТ ЖИЛОГО ПОМЕЩЕНИЯ И КОММУНАЛЬНЫЕ УСЛУГИ</p>
		<table border='1' class='billtable-table' align='center'>
			<tr>
				<th>Услуга</th>
				<th>Тариф</th>
				<th>Норма</th>
				<th>Ед.Изм</th>
				<th>Объем потребл.</th>
				<th>Начислено</th>
				<th>Перерасчет</th>
				<th>Итого начислено</th>
				<?php
					$srvblock=Array(0=>Array(110,1600,500,600,700,900,1200),1=>Array(175,176,177,178,179,180));
					$allsaldo = 0;
					$allpay = 0;
					if(isset($Form['bill']))
					foreach($Form['bill'] as $srv => $val){
						$allsaldo+=(isset($val['saldo']) ? $val['saldo'] : 0);
						$allpay+=(isset($val['pay']) ? $val['pay'] : 0);
					}
				
				
					$allacr = 0;
					$allrec = 0;
					$allitog = 0;
					foreach($srvblock as $ix => $usrv){
						$bacritog = 0;
						$brecalcitog = 0;
						$bacrallitog = 0;
						if(isset($Form['bill']))
						foreach($Form['bill'] as $srv => $val){
							if(in_array($srv,$usrv)){
								$recalc = isset($val['recalc']) ? $val['recalc']: 0;
								$accrual = isset($val['accrual']) ? $val['accrual']: 0;
								$total = $recalc+$accrual;
								$pay  =isset($val['pay']) ? $val['pay']: 0;
								$info = $val['info'];
				
								
								echo "<tr>";
									echo "<td>".$info['name']."</td>";
									if(!$ix){
										echo "<td>".$info['price']."</td>";
										echo "<td>".$info['volume']."</td>";
										echo "<td>".$info['measure']."</td>";
										echo "<td>".$info['usedvolume']."</td>";
									}
									else{
										echo "<td>".$info['calcprice']."</td>";
										
										echo "<td>-</td>";
										echo "<td>м<sup>2</sup></td>";
										echo "<td>".$Form['account']['square']."</td>";
									}
	
									echo "<td>".number_format($accrual, 2, ',', ' ')."</td>";
									echo "<td>".number_format($recalc, 2, ',', ' ')."</td>";
									echo "<td>".number_format($total, 2, ',', ' ')."</td>";

								echo "</tr>";
								$bacritog += $accrual;
								$brecalcitog += $recalc;
								$bacrallitog += $total;
								$allacr += $accrual;
								$allrec += $recalc;
								$allitog += $total;	
							}
						}
							echo "<tr>";
								echo "<th colspan='5'>ИТОГО:</th>";
								echo "<th>".number_format($bacritog, 2, ',', ' ')."</th>";
								echo "<th>".number_format($brecalcitog, 2, ',', ' ')."</th>";
								echo "<th>".number_format($bacrallitog, 2, ',', ' ')."</th>";
							echo "</tr>";
					}
					echo "<tr>";
						echo "<th colspan='5'>ВСЕГО:</th>";
						echo "<th>".number_format($allacr, 2, ',', ' ')."</th>";
						echo "<th>".number_format($allrec, 2, ',', ' ')."</th>";
						echo "<th>".number_format($allitog, 2, ',', ' ')."</th>";
									
					echo "</tr>";	
				?>
			</tr>		
		</table>
		<hr>
		<div class='billtable-foot-layout'>
			<table class='billtable-table' border='1'>
				<tr>
					<th></th>
					<th>Итого</th>
				</tr>
				<tr>
					<td>Долг на <?php echo strftime("01.%m.%Y",strtotime($Form['period'])); ?>:</td>
					<td><?php echo number_format($allsaldo, 2, ',', ' '); ?></td>
				</tr>
				<tr>
					<td>Начислено:</td>
					<td><?php echo number_format($allitog, 2, ',', ' '); ?></td>
				</tr>
				<tr>
					<td>Оплачено:</td>
					<td><?php  echo number_format($allpay, 2, ',', ' ');  ?></td>
				</tr>
				<tr>
					<td>К оплате:</td>
					<td><?php echo number_format(($allsaldo + $allitog - $allpay), 2, ',', ' '); ?></td>
				</tr>	
			</table>
		</div>
		<div class='billtable-foot-layout'>
			
		</div>
	</div>
</div>
</div>
<?php

?>