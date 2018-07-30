function DrawHistogramm(name,imgWidth,imgHeight,period,minval,maxval,data,type,dir){
	
		var hist = document.getElementById(name);
	   	var hDC = hist.getContext("2d");
	   	var sFoot = 40;
	   	var fib = 60;
	   	var gistHeight = imgHeight-sFoot;
	   	var blStep = 30;
	   	var wStep = (imgWidth-fib) / period;
	   	var curDate = new Date();
	   	var options = {year: 'numeric',month: 'short'};
	   	var style = (type==undefined?0:type);
	   	var linescolor=Array('blue','red','green','yellow','maroon','silver');

	   	
	   	hDC.lineDashOffset=0.5;
	   	hDC.lineWidth=1;
	   	hDC.fillStyle = 'rgba(80,80,80,1)';
	   	
	   	hDC.fillRect(5,imgHeight-sFoot,imgWidth-10,3);
		
	   	hDC.fillRect(fib-1,5,2,imgHeight-sFoot-5);
	   	
	 	k = (gistHeight-10) / (maxval-minval);
	   	
	   	
	   	
	   		
	   	hDC.textAlign = "right";
		for(a=gistHeight;a>0;a-=blStep){	
			hDC.fillStyle = 'rgba(200,150,150,1)'; 	
			hDC.fillRect(5,a,imgWidth-10,1); 
			hDC.fillStyle = 'rgba(0,0,0,1)'; 	 
			hDC.fillText(Number(((gistHeight-a)/k)+minval).toFixed(0), fib-5,a-1, 300);	
		}
		
		
		mix = imgWidth/wStep;
		curDate.setMonth(curDate.getMonth() - mix);
		hDC.textBaseline = "top";
		
		keys = Object.keys(data);
		line = data[keys[0]];
		lineLength =  Math.min(Object.keys(line).length,period); 
		
	
		ix = 0;
		hDC.textAlign = "center";
		
		if(dir){
			 lPos = fib;
			 stp = wStep;
		}
		else{
			lPos = fib + (lineLength-1)*wStep;
			stp = -wStep;
		}
		p = 0;
		for(key in line){
			
			hDC.fillStyle = 'rgba(150,150,150,1)'; 	
			hDC.fillRect(lPos,5,1,imgHeight-sFoot-5); 
			
			hDC.fillStyle = "#000";
			hDC.fillText(key, lPos,gistHeight+5, wStep-10);	
			
			lPos+=stp;
			p++;	
			if(p>=lineLength) break;		
		}
		
		
		
		la = a-wStep;
		hDC.lineDashOffset=1;
	   	hDC.lineWidth=1;

		offset = minval*k;
		hDC.fillStyle = 'white';
			
		legPos = 20;
		l = 0;
		
		if(dir) lPos = fib;
		else lPos = fib + (lineLength-1)*wStep;

		for(lineix in data){
			hDC.beginPath();
	
			p = 0;
			if(dir) lPos = fib;
			else lPos = fib + (lineLength-1)*wStep;
			for(pointix in data[lineix]){
				point = data[lineix][pointix];
				if(!p) hDC.moveTo(lPos,imgHeight-sFoot-(point*k) + offset);
				y = imgHeight-sFoot-(point*k)+ offset;				
					
				hDC.lineTo(lPos,y);
				
					
				hDC.arc(lPos, y, 1, 0, 2*Math.PI, false);
				hDC.arc(lPos, y, 2, 0, 2*Math.PI, false);
				
				hDC.moveTo(lPos,y);
    			
					
				lPos+=stp;
				p++;
				if(p>=lineLength) break;
				
			}
				
			hDC.fillStyle = linescolor[l];
			hDC.strokeStyle = linescolor[l++];

			hDC.stroke();
			

			hDC.textAlign = "left";
			hDC.fillRect(legPos,imgHeight-14,15,10);	
			hDC.fillText(lineix, legPos+17,imgHeight-14);
			
			tp = hDC.measureText(lineix);
			legPos+=(tp.width+25);		 					
		}			
}