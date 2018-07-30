<header>
<div class='title-layout'>
	<div class='title-element title-logo'></div>
	<div class='title-element title-name filter-shadow'><?php echo $GLOBALS['settings']['title'] ?></div>
</div>
<div class='title-layout'>
	<div class='title-element org-info title-org-info'>
		<p><img src='templates/big/images/icons/icon_house_black.png' width='32px' height='32px'><?php echo $GLOBALS['organization']['location'] ?></p>
		<p><img src='templates/big/images/icons/icon_phone_black.png' width='32px' height='32px'><?php echo str_replace("-","&#8209;",$GLOBALS['organization']['phones']); ?></p>
		<p><img src='templates/big/images/icons/icon_email_black.png' width='32px' height='32px'><?php echo $GLOBALS['organization']['e-mail'] ?></p>
	</div>
</div>

<div class='title-layout'>
	<nav>
	<div id='nav' class='header-main-menu'>
		<ul class='main-menu-main'>
		<?php
			$menu = $GLOBALS['menu'];

			for($pp=0;$pp<count($menu);$pp++){
				
				if(isset($menu[$pp]['submenu'])){
					echo "<li><a><img src='".$menu[$pp]['image']."'>".$menu[$pp]['text']."</a>";
					echo "<ul class='main-menu-sub'>";
					$smenu = $menu[$pp]['submenu'];
				
					for($ppp=0;$ppp<count($smenu);$ppp++){
						echo "<li><a href='".$smenu[$ppp]['link']."'><img src='".$smenu[$ppp]['image']."'>".$smenu[$ppp]['text']."</a></li>";	
					}
					echo "</ul>";
				}
				else echo "<li><a href='".$menu[$pp]['link']."'><img src='".$menu[$pp]['image']."'>".$menu[$pp]['text']."</a>";
				echo "</li>";
			}
		
		?>		
		</ul>	
		</div>
	</nav>
	<script> 
		var iMenu = $('#nav');		
		var menuPos = iMenu.offset().top + window.pageYOffset;
		
		window.onresize = function(){
			menuPos = iMenu.offset().top + window.pageYOffset;
			if (iMenu.hasClass('main-menu-fixed') && window.pageYOffset < menuPos) {
				iMenu.removeClass('main-menu-fixed');
			}
			else if(window.pageYOffset > menuPos) {
				iMenu.addClass('main-menu-fixed');
			}
		}
		window.onscroll = function(){
			if (iMenu.hasClass('main-menu-fixed') && window.pageYOffset < menuPos) {
				iMenu.removeClass('main-menu-fixed');
			}
			else if(window.pageYOffset > menuPos) {
				iMenu.addClass('main-menu-fixed');
			}
		};
	</script>
</div>
<div class='title-layout'>
	<article class='page-lister'>
		<?php 
		$com = $GLOBALS['command'];
		$mod = $GLOBALS['module'];
		$chapter = $com['chapter'];
		$user = GetCurrentUser();
		
		
		echo "<div class='page-lister-element'><a href='index.php'>Главная</a></div>";

			switch($com['command']){
				case doShow:
					$chap = "<div class='page-lister-element'><a href='".makeLink($com['mod'],$com['chapter'],$com['tab'],doList,$com['type'],0)."'>".$GLOBALS['chapters'][$chapter]."</a></div>";
					$ipl = coreSafeFunction('GetLister');
					if($ipl) 	echo $chap.$ipl;
					else		echo $chap;
				break;
				default:
					$ipl = coreSafeFunction('GetLister');
					if($ipl) echo $ipl;
				break;
				
			}

	 ?>
	</article>
</div>
</header>
<main>