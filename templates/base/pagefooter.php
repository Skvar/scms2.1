</main>
<footer>
	<div class='footer-logo footer-element'></div>
	<div class='footer-element'>
		<div class='org-info footer-org-info'>
			<p><?php echo $GLOBALS['organization']['name'] ?></p>
			<p><img src='images/icons/home.png'><?php echo $GLOBALS['organization']['location'] ?></p>
			<p><img src='images/icons/small_phone.png'><?php echo $GLOBALS['organization']['phones'] ?></p>
			<p><img src='images/icons/small_mail.png'><?php echo $GLOBALS['organization']['e-mail'] ?></p>
		</div>	
	</div>
	<div id='footnav' class='footer-main-menu footer-element'>
	<ul class='footer-menu-main'>
	<?php
		$menu = $GLOBALS['menu'];

		for($pp=0;$pp<count($menu);$pp++){
			if(isset($menu[$pp]['footmenu'])){
				echo "<li><a href='".$menu[$pp]['link']."'><img src='".$menu[$pp]['image']."'>".$menu[$pp]['text']."</a>";
				if(isset($menu[$pp]['submenu'])){
					echo "<ul class='footer-menu-sub'>";
					$smenu = $menu[$pp]['submenu'];
				
					for($ppp=0;$ppp<count($smenu);$ppp++){
						echo "<li><a href='".$smenu[$ppp]['link']."'>".$smenu[$ppp]['text']."</a></li>";	
					}
					echo "</ul>";
				}
				echo "</li>";
			}
		}
	
	?>		
	</ul>	
	</div>
	<div class='footer-copyright footer-element'>
		<p class='master'> Skvar.dig.lion SCMS v0.5</p>
	</div>


</footer>