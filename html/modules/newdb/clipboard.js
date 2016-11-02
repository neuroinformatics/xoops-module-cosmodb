function setClipboard(data){

	data = "Z:/" + data;
	var rgexp = new RegExp('/', 'g');
	data = data.replace(rgexp, '\\');
	
	var ua = navigator.userAgent.toLowerCase(); 
	var is_pc_ie = ((ua.indexOf('msie') != -1) && (ua.indexOf('win') != -1) && (ua.indexOf('opera') == -1) && (ua.indexOf('webtv') == -1));

	if(is_pc_ie){
		window.clipboardData.setData('text', data);

	}else{
		var swf = "<embed src='include/setClipboard.swf' FlashVars='code="+encodeURI(data)+"' width='0' height='0' type='application/x-shockwave-flash'></embed>";
		document.getElementById('copy').innerHTML = '';
		document.getElementById('copy').innerHTML = swf;
		if(document.getElementById('copy').value == ''){
			alert('NG');
		}
	}
}
