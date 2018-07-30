<?php
//---------------------------------------------------------
function tPutHeader()
{
include("pageheader.php");
$GLOBALS['template']['count'] = 0;	
	
}

//---------------------------------------------------------
function tInsertContentHeader($out = Array(),$style='block')
{
	echo "<div class='page-title $style'>";
	if(isset($out['backbutton'])){
		echo "<div class='page-title-backbutton'><a href=".$out['backbutton'].">&nbsp;</a></div>";
	}
	echo $out['pagetitle'];
	echo "</div>";
	
	
}
//---------------------------------------------------------	
function tInsertPost($out = Array(),$style='block',$showbutton = true)
{
	echo "<div class='page-post-block $style'>";
		
		echo "<div class='page-post-block-info $style-info'>";
		echo "</div>"; 
		
		echo "<div class='page-post-block-post $style-post'>";
			echo "<div class='page-post-block-header $style-header'>";
			if($showbutton) tInsertSmallButtons($out['handler'],"id=".$out['id'].(isset($out['type']) ? "&type=".$out['type']:""),Array('edit'=>doEdit,'delete'=>doDelete));
			echo (isset($out['postheader']) ? $out['postheader']: "")."</div>"; 
			
			echo "<div class='page-post-block-text $style-text'>".(isset($out['post']) ? $out['post']: "");
				echo "<div class='page-post-block-footer $style-footer'>";
					echo "<div class='page-post-block-date $style-date'>".(isset($out['postdate']) ? $out['postdate']: "")."</div>";
					echo "<div class='page-post-block-user $style-user'>".(isset($out['user']) ? $out['user']: "")."</div>";
				echo "</div>";	
			echo "</div>"; 
			
		echo "</div>";
		
		if($showbutton){	
				if(isset($out['handler']) && isset($out['arg']))		
				echo 	"<button type='button' class='normal-button' style='position:absolute;top:8px;right:70px' onclick='Commandsd(\"".$out['handler']."\",\"".$out['arg']."&ajax=1\")'>
				 			<div class='button-icon button-icon-ok'></div>
				 			Скрыть.
				 		</button>";		
		}
	echo "</div>"; 	
}
//---------------------------------------------------------
function tInsertHierarchyBlock($out = Array(),$style='block',$open = false)
{
	$count = $GLOBALS['template']['count'];
	$user = GetCurrentUser();
	
	echo "<div class='page-hierarchy $style ".($open?"page-hierarchy-open":"")."'  id='$style-$count'>";
	
		echo "<div class='page-hierarchy-header $style-header' ".(isset($out['anchor'])?'name='.$out['anchor'] : "")."  onclick='ToggleOpenBlock(\"$style-$count\");'>";
		if(isset($out['icon'])) echo "<img src='".$out['icon']."'>";
		echo $out['header'];
			if($user['right']>=100 && isset($out['id']) && isset($out['handler'])){	
					$handler = $out['handler'];
					$arg1 = "&ajax=1&command=".doEdit."&id=".$out['id'];
					$arg2 = "&ajax=1&command=".doDelete."&id=".$out['id'];
					echo "<div class='hierarchy-element-butt-enchor'>
							<div class='v-small-button button-icon-edit' style='top:5px;right:23px;' onclick='Commandsd(\"$handler\",\"$arg1\");'></div>
							<div class='v-small-button button-icon-delete' style='top:5px;right:5px;' onclick='Commanddel(\"$handler\",\"$arg2\");'></div>	
						 </div>";
			}
		echo "</div>";
		
		echo "<div class='page-hierarchy-body  $style-body'>";
		foreach($out['out'] as $key => $val){
			if($user['right']>=100 && isset($val['id']) && isset($out['handler'])){
				
				$handler = $out['handler'];
				$arg1 = "&ajax=1&command=".doEdit."&id=".$val['id'];
				$arg2 = "&ajax=1&command=".doDelete."&id=".$val['id'];
				echo "<div class='hierarchy-element-butt-enchor'>
						<div class='v-small-button button-icon-edit' style='top:5px;right:23px;' onclick='Commandsd(\"$handler\",\"$arg1\");'></div>
						<div class='v-small-button button-icon-delete' style='top:5px;right:5px;' onclick='Commanddel(\"$handler\",\"$arg2\");'></div>	
					 </div>";
				
			}
			
			echo "<div class='page-hierarchy-block $style-block' ".(isset($val['background']) ? ("style='background-image:url(\"./images/logo_street.png\")'") : "").">";
					echo "<div class='page-hierarchy-block-header $style-block-header'>".$val['header']."</div>";
					echo "<div class='page-hierarchy-block-text $style-block-text'>".$val['text']."</div>";
		
			echo "</div>";
			
			
			
					
		}
		echo "</div>";
		
	
	echo "</div>";	
	$GLOBALS['template']['count']++;
}
//---------------------------------------------------------
function tInsertContentFooter($out = Array())
{
	
}
//---------------------------------------------------------
function tInsertSmallButtons($handler,$arg,$buttons)
{
	$user=GetCurrentUser();
	if($user['right']<=100) return;
	echo "<div class='buttons-anchor'>";
		$x = 19*(count($buttons)-1)+2;
		foreach($buttons as $button=>$com){
			if($button=='delete'){
				 echo "<div class='v-small-button button-icon-$button' style='top:2px;right:".$x."px;' onclick='Commanddel(\"$handler\",\"&ajax=1&$arg&command=$com\");'></div>";
			}
			else{
				 echo "<div class='v-small-button button-icon-$button' style='top:2px;right:".$x."px;' onclick='Commandsd(\"$handler\",\"&ajax=1&$arg&command=$com\");'></div>";
			}
			$x-=19;
		}
		
		echo "</div>";		
}

//---------------------------------------------------------
function tPutFooter()
{
include("pagefooter.php");		
}
//---------------------------------------------------------
function iInsertPages($out=Array(),$active)
{
	$uri = $GLOBALS['command']['uri'];
	
	echo "<div class='pagetab'>";
		$ix = 0;
		foreach($out as $key=>$val){
			if($val){
				
			}
			else{
				if($ix==$active)	echo "<div class='pagetab-tab pagetab-tab-active'>".$key."</div>";
				else				echo "<div class='pagetab-tab'><a href='index.php?".coreChangeURI($uri,'tab',$ix)."'>".$key."</a></div>";
			}
			
			
			
			$ix++;
		}
	
	
	echo "</div>";
}


//---------------------------------------------------------
function tAuthPanel($out=Array())
{
	echo "<form method='POST'>";
		echo "<div class='auth-panel'>";
			echo "<div class='auth-panel-header'>";
			echo (isset($out['header-icon']) ? ("<img src='".$out['header-icon']."'>"):" ");
			echo (isset($out['header'])?$out['header']:"");
			echo "</div>";
			echo "<div class='auth-panel-string'>";
				echo "<label for='user-name'>".(isset($out['user-name'])? $out['user-name']:"Имя пользователя");	
				echo "</label>";
				echo "<input type='text' name='user-name' id='user-name' placeholder='".(isset($out['user-name-plc'])?$out['user-name-plc']:"")."' required>";
			echo "</div>";
			echo "<div class='auth-panel-string'>";
				echo "<label for='user-pwd'>".(isset($out['user-pwd'])? $out['user-pwd']:"Пароль");
				echo "</label>";
				echo "<input type='password' required name='user-pwd'  id='user-pwd' placeholder='".(isset($out['user-pwd-plc'])?$out['user-pwd-plc']:"")."' ".(isset($out['user-pwd-pattern'])?"pattern='".$out['user-pwd-pattern']."'":"").">";
			echo "</div>";	
			echo "<div class='auth-panel-footer'>";
			echo "<button type='submit' class='normal-button'>";
			echo "<div class='button-icon button-icon-ok'></div>";
			echo "Вход";
			echo "</button>";
			echo "<button type='reset' class='normal-button'>";
			echo "<div class='button-icon button-icon-reset'></div>";
			echo "Сброс";
			echo "</button>";
			echo "</div>";
		echo "</div>";
	echo "</form>";
}

?>