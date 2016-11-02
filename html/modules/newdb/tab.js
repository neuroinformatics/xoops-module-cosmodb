
// bpref = tab body prefix, hpref = tab header prefix
function seltab(bpref, hpref, id_max, selected) {
	if (! document.getElementById) return;
	for (i = 0; i <= id_max; i++) {
		if (! document.getElementById(bpref + i)) continue;
		if (i == selected) {
			document.getElementById(bpref + i).style.display = "block";
		} else {
			document.getElementById(bpref + i).style.display = "none";
		}
	}
}

function seltab_all_close(bpref) {
	if (! document.getElementById) return;
	for (i = 1;; i++) {
		if (! document.getElementById(bpref + i)) break;
		document.getElementById(bpref + i).style.display = "none";
	}
}

function seltab_all_open(bpref) {
	if (! document.getElementById) return;
	for (i = 1;; i++) {
		if (! document.getElementById(bpref + i)) break;
		document.getElementById(bpref + i).style.display = "block";
	}
}


function check_all(pref, flg){
	if (! document.getElementById) return;
	for (i = 1;; i++) {
		if (! document.getElementById(pref + i)) break;
		if(flg == '1'){
			document.getElementById(pref + i).checked = true;
		}else{
			document.getElementById(pref + i).checked = false;
		}
	}
}