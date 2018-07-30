
function testCon(str)
{
	alert(str);
}




function WriteFormData(handler,form,com)
{
	
	arr = $('#'+form).find('input');
	arr1 = $('#'+form).find('select');
	arr2 = $('#'+form).find('textarea');
						
	$.merge(arr, arr1);
	$.merge(arr, arr2);
	
	
	var values = {};				
	jQuery.each(arr, function(i, val) {
		 values[val.id] = val.value;	
	});	
	
	if(typeof ckEditorUsed !== "undefined"){
		values[ckEditorUsed] = CKEDITOR.instances[ckEditorUsed].getData();
	}
		
	string = JSON.stringify(values);  	
	string = encodeURIComponent(string);	
	com = com+"&data="+string+"&ajax=1";	

	SendRequest(handler,com,function(req){
		if(req.responseText.length > 0){
			try{
	        	 obj = JSON.parse(req.responseText);
	        	 if(obj['result'] == true){	
	        	 	if(obj['reload'] == true) location.reload();	
	        	 	if(obj['output']!=undefined){        	 		
	        	 		 $('#' + obj['output']).html(obj['message']);	
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
  /* 
function encodeHTML(raw){
  return raw.replace(/[\u00A0-\u9999<>\&]/gim, i => '&#' + i.charCodeAt(0) + ';');
}
*/


function Commanddel(handler,com){
	
		this.event.stopPropagation();
		Overlay(true);
		jConfirm("Вы точно хотите удалить этот объект?","Удаление записи",function(res){
				if(res==true){
					Commandsd(handler,com);
				}
				else Overlay(false);
		});		
}


function Commandsd(handler,com,u_confirm)
{
	this.event.stopPropagation();
	SendRequest(handler,com,function(req){
		if(req.responseText.length > 0){
			try{
	        	 obj = JSON.parse(req.responseText);
	        	 	
	        	 if(obj['result'] == true){	
	        	 	if(obj['useform'] == true) 	form = CreateForm(800,200,obj);
	        	 	if(obj['output']!=undefined){	   
	        	 		 $('#'+obj['output']).html(obj['message']);	
	        	 	}
	        	 	if(obj['reload'] == true) location.reload();
	        	 	else if(obj['outresult']){
						jAlert(obj['message'],"Операция выполнена успешно",function(){
							Overlay(false);
						});	 	
					}	   	
					return true;		
				} 
				else{
					jAlert(obj['message'],"Ошибка операции",function(){
						Overlay(false);
					});
				}
	       	}    
	        catch (CatchException){
	        	 console.log(req.responseText);
	        }						
		}
	});
}

//-----------------------------------------------------------------------------
var topWindow=50;
function CreateForm(w,h,obj)
{
	headericon = obj['headericon'];
	
	
	
	if($('#dummy-container').length > 0){
		Overlay(false);
		$('#dummy-container').remove();	
	}
	else{
		px = $(document).width()/2 - w/2;
		py = $(document).height()/2 - h/2;
		Overlay(true);	
		$("BODY").append("<div id='dummy-container'></div>");
		$("#dummy-container").css({top: (topWindow + window.pageYOffset) + 'px',left: px + 'px',width: w+'px','min-height': h+'px'});	
		$("#dummy-container").addClass('form-window');
		ShowSpinner('dummy-container');
		
		form = "";
		form += "<div id='dummy-container-header' class='form-window-header'></div>";
		form += "<div id='dummy-container-body' class='form-window-body'></div>";
		form += "<div id='dummy-container-user' class='form-window-user'></div>";
		$("#dummy-container").html(form);
		
				
		$("#dummy-container").animate({opacity:'1'},'fast',function(){
			if(obj['headericon']!=undefined) 	img = "<img src='" + obj['headericon'] + "'/>";
			else 						img = "";
			$("#dummy-container-header").html(img + "<p>" + obj['header'] + "</p>");
			$("#dummy-container-body").html(obj['message']);
			
			$("#dummy-container-user").html("<p>Текущий пользователь: " + obj['user'] + "</p>");
		
			
		});		
	}
	topWindow+=30;
}

function CloseForm()
{
	topWindow-=30;
	CloseView();
}





//-----------------------------------------------------------------------------
function CloseView()
{
	if($('#dummy-container').length > 0){
		Overlay(false);
		$('#dummy-container').remove();	
	}
}

//-----------------------------------------------------------------------------
function ShowDoc(doc)
{
	if($('#dummy-container').length > 0){
				Overlay(false);
			 	$('#dummy-container').remove();	
		}
		else{	
			Overlay(true);	
				$("BODY").append("<div id='dummy-container'></div>");
				$("#dummy-container").css({
					position: 'absolute',
					zIndex: '100',
					top: 10+window.pageYOffset+'px',
					left: '5%',
					width: '90%',
					height: '90%',
					background: '#FFFFFF',
					opacity: '0',
					border:'2px solid gray',
					boxShadow:'5px 5px 10px 0 gray',
				});	
				//$("#dummy-container").on('click',CloseView);
				clsButt = "<a class='form-close-button form-close-doc' onclick='CloseView();'></a>";
			
				
				
				$("#dummy-container").animate({opacity:'1'},'fast',function(){
					$("#dummy-container").html("<iframe onload='HideSpinner' src='https://docs.google.com/viewer?url=https://xn-----6kcbhdo1dhthggwt2fyf.xn--p1ai/" + doc + "&embedded=true' width='100%' height='100%'></iframe>");
					ShowSpinner('dummy-container');
					$("#dummy-container").append(clsButt);
					$('iframe').on('load', HideSpinner);
					
				});			 	
	}	
	
}

//-----------------------------------------------------------------------------
function ShowImage(image,w,h){
	
			var cw = $(document).width()-20;
			var ch = $(document).height()-60;
			
			var kw = w/(cw);
			var kh = h/(ch);
			var k =  Math.max(kw,kh);
			
			var px = (cw/2 - (w/k)/2);
			var py = (ch/2 - (h/k)/2)+window.pageYOffset;
	
		if($('#dummy-container').length > 0){
				Overlay(false);
			 	$('#dummy-container').remove();	
		}
		else{	
			Overlay(true);	
				$("BODY").append("<div id='dummy-container'></div>");
				$("#dummy-container").css({
					position: 'absolute',
					zIndex: '100',
					top: py+'px',
					left: px+'px',
					width: (w/k)+'px',
					height: (h/k)+'px',
					background: '#FFFFFF',
					opacity: '0',
					border:'2px solid gray',
					boxShadow:'5px 5px 10px 0 gray',
				});	
				//$("#dummy-container").on('click',CloseView);
				ShowSpinner('dummy-container');
				clsButt = "<a class='form-close-button form-close-button-image' onclick='CloseView();'></a>";
				
				$("#dummy-container").animate({opacity:'1'},'fast',function(){
					$("#dummy-container").html("<img src='" + image + "' width='" +(w/k)+ "' height='"+ (h/k) +"'>");
					$("#dummy-container").append(clsButt);
				});			 	
		}		
}



//-----------------------------------------------------------------------------
function Overlay(show)
{
	if(show){
		if(!$('#FormOverlay').length){
			$("BODY").append("<div id='FormOverlay'></div>");
			$("#FormOverlay").css({
					position: 'absolute',
					zIndex: '99',
					top: '0px',
					left: '0px',
					width: '100%',
					height: $(document).height()+'px',
					background: '#000000',
					opacity: '0.6'
				});	

			$("#FormOverlay").on('click',CloseView);
		}
	}
	else{
		if($('#FormOverlay').length > 0) $('#FormOverlay').remove();
	}
}
//-----------------------------------------------------------------------------
var npop = 1;
function PopupMessage(target,message)
{
	ClosePopups();
	var ovr = document.createElement("div");
	ovr.classList.add('popup-message');
	ovr.id='Popup'+npop;
	npop++;
	ovr.style.zIndex = 101;
	ovr.onclick = ClosePopups;
	ovr.innerHTML = message;
	document.body.insertBefore(ovr, document.body.firstElementChild);
	var pop = ovr.getBoundingClientRect();
	
	//alert(pop.bottom + "  " + pop.top);
	
	var box = getCoords(target);
	ovr.style.left = (box.left) + "px";
	ovr.style.top = (box.top - pop.height-10) + "px";
	
			
	
	
}
//-----------------------------------------------------------------------------
function ClosePopups()
{
	var divs = document.getElementsByTagName("div");
	for(a=0;a<divs.length;a++){
		if(divs[a].id.substr(0,5)=="Popup") divs[a].parentNode.removeChild(divs[a]);		
	}
	
}
//-----------------------------------------------------------------------------
function getCoords(elem) { // кроме IE8-
  var box = elem.getBoundingClientRect();

  return {
    top: box.top + pageYOffset,
    left: box.left + pageXOffset,
    width: box.right - box.left,
    height: box.bottom - box.top
  };

}


//-----------------------------------------------------------------------------
var spinner=null;
function ShowSpinner(parent)
{
	var target = null;
	HideSpinner();
	if(target==null) target = document.getElementById(parent);
	var opts = {
		  lines: 20 // The number of lines to draw
		, length: 5 // The length of each line
		, width: 3 // The line thickness
		, radius: 10 // The radius of the inner circle
		, scale: 1 // Scales overall size of the spinner
		, corners: 1 // Corner roundness (0..1)
		, color: '#A00000' // #rgb or #rrggbb or array of colors
		, opacity: 0.2 // Opacity of the lines
		, rotate: 0 // The rotation offset
		, direction: 1 // 1: clockwise, -1: counterclockwise
		, speed: 1.0 // Rounds per second
		, trail: 100 // Afterglow percentage
		, fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
		, zIndex: 2e9 // The z-index (defaults to 2000000000)
		, className: 'spinner' // The CSS class to assign to the spinner
		, top: '50%' // Top position relative to parent
		, left: '50%' // Left position relative to parent
		, shadow: false // Whether to render a shadow
		, hwaccel: true // Whether to use hardware acceleration
		, position: 'absolute' // Element positioning
	}
	spinner = new Spinner(opts).spin(target);
}
//-----------------------------------------------------------------------------
function HideSpinner()
{
	if(spinner) spinner.stop();	
}

//-----------------------------------------------------------------------------
function scrollToAnchor(anchor){
      $('html,body').animate({scrollTop:$('[name="'+anchor+'"]').offset().top-100},300);
}
 //-----------------------------------------------------------------------------
function BlockForm(formname,block)
{

	arr = $('#'+formname).find('button');
	
	jQuery.each(arr, function(i, val) {
		if(val.id!=""){
			if(block)	$('#'+val.id).prop('disabled',true);
			else		$('#'+val.id).prop('disabled',false);
		}
	});	
		
}

	






















