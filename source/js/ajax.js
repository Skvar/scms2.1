//------------------------------------------------------------------------------------
function CreateRequest()
{
	    var Request = false;
	    if (window.XMLHttpRequest) Request = new XMLHttpRequest();
	    else if (window.ActiveXObject){
	        try{
	        	 Request = new ActiveXObject('Microsoft.XMLHTTP');
	       	}    
	        catch (CatchException){
	        	 Request = new ActiveXObject('Msxml2.XMLHTTP');
	        }
	    } 
	    if (!Request) alert('Невозможно создать XMLHttpRequest');  
	    return Request;
}
//----------------------------------------------------------------------------------
function SendRequest(r_path, r_args, r_handler)
{
    var Request = CreateRequest();
    
    if (!Request)return;

    Request.onreadystatechange = function(){
        if (Request.readyState == 4){
        	if(r_handler !== undefined)  r_handler(Request);
        }
    }
    
    
    Request.open('post', r_path, true);
    Request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");
    Request.send(r_args);

} 
