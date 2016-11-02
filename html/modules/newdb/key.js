
function notStr(kwid) {
	if (! document.getElementById) return;

	if(document.getElementById(kwid).style.color == 'red'){
		document.getElementById(kwid).style.color = 'black';
	} else {
		document.getElementById(kwid).style.color = 'red';
		if(document.getElementById('c'+kwid).checked){
			document.getElementById('c'+kwid).checked = false;
		}
	}
	document.getElementById('notkws').value+=kwid + ',';
}

function notCheck(kwid) {
	if (! document.getElementById) return;

	if(document.getElementById(kwid).style.color == 'red'){
		document.getElementById(kwid).style.color = 'black';
		document.getElementById('notkws').value+=kwid + ',';
	}
}
