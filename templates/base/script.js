function ToggleOpenBlock(name){
	block = document.getElementById(name);
	if(block!=null){
		p = block.className.split(" ");
		opc = p[0] + '-open';
		if(block.classList.contains(opc)) block.classList.remove(opc);
		else 							block.classList.add(opc);
	}
	
}


