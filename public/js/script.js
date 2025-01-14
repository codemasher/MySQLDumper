function obj_enable(objid){
	let e = document.getElementById(objid);
	e.disabled = false;
}

function obj_disable(objid, state){
	let e = document.getElementById(objid);
	e.disabled = !state;
}

function setactiveMenuFromContent(){
	let a = parent.MySQL_Dumper_content.location.href;
	let menuid = 1;
	if(a.indexOf('config_overview.php') !== -1){
		menuid = 2;
	}
	if(a.indexOf('filemanagement.php') !== -1){
		if(a.indexOf('action=dump') !== -1){
			menuid = 3;
		}
		if(a.indexOf('action=restore') !== -1){
			menuid = 4;
		}
		if(a.indexOf('action=files') !== -1){
			menuid = 5;
		}
	}
	if(a.indexOf('sql.php') !== -1){
		menuid = 6;
	}
	if(a.indexOf('log.php') !== -1){
		menuid = 7;
	}
	if(a.indexOf('help.php') !== -1){
		menuid = 8;
	}
	setMenuActive('m' + menuid);
}

function setMenuActive(id){
	for(let i = 1; i <= 10; i++){
		let objid = 'm' + i;
		if(id === objid){
			parent.frames[0].document.getElementById(objid).className = 'active';
		}
		else{
			if(parent.frames[0].document.getElementById(objid)){
				parent.frames[0].document.getElementById(objid).className = '';
			}
		}
	}
}

function GetSelectedFilename(){
	let a = '';
	let obj = document.getElementsByName('file[]');
	let anz = 0;
	if(!obj.length){
		if(obj.checked){
			a += obj.value;
		}
	}
	else{
		for(i = 0; i < obj.length; i++){
			if(obj[i].checked){
				a += '\n' + obj[i].value;
				anz++;
			}
		}
	}
	return a;
}

function Check(i, k){
	let anz = 0;
	let s = '';
	let smp;
	let ids = document.getElementsByName('file[]');
	let mp = document.getElementsByName('multipart[]');
	for(let j = 0; j < ids.length; j++){
		if(ids[j].checked){
			s = ids[j].value;
			smp = !mp[j].value ? '' : ' (Multipart: ' + mp[j].value + ' files)';
			anz++;
			if(k === 0){
				break;
			}
		}
	}
	if(!anz){
		WP('', 'gd');
	}
	else if(anz){
		WP(s + smp, 'gd');
	}
	else{
		WP('> 1', 'gd');
	}
}

function SelectMD(v, anz){
	for(let i = 0; i < anz; i++){
		let n = 'db_multidump_' + i;
		let obj = document.getElementsByName(n)[0];
		if(obj && !obj.disabled){
			obj.checked = v;
		}
	}
}

function Sel(v){
	let a = document.frm_tbl;
	if(!a.chk_tbl.length){
		a.chk_tbl.checked = v;
	}
	else{
		for(let i = 0; i < a.chk_tbl.length; i++){
			a.chk_tbl[i].checked = v;
		}
	}
}

function ConfDBSel(v, adb){
	for(let i = 0; i < adb; i++){
		let a = document.getElementsByName('db_multidump[' + i + ']');
		if(a){
			a.checked = v;
		}
	}
}

function chkFormular(){
	let a = document.frm_tbl;
	a.tbl_array.value = '';
	if(!a.chk_tbl.length){
		if(a.chk_tbl.checked){
			a.tbl_array.value += a.chk_tbl.value + '|';
		}
	}
	else{
		for(let i = 0; i < a.chk_tbl.length; i++){
			if(a.chk_tbl[i].checked){
				a.tbl_array.value += a.chk_tbl[i].value + '|';
			}
		}
	}
	if(a.tbl_array.value === ''){
		alert('Choose tables!');
		return false;
	}
	else{
		return true;
	}
}

function insertHTA(s, tb){
	let ins;
	if(s === 1){
		ins = 'AddHandler php-fastcgi .php .php4\nAddhandler cgi-script .cgi .pl\nOptions +ExecCGI';
	}
	if(s === 101){
		ins = 'DirectoryIndex /cgi-bin/script.pl';
	}
	if(s === 102){
		ins = 'AddHandler cgi-script .extension';
	}
	if(s === 103){
		ins = 'Options +ExecCGI';
	}
	if(s === 104){
		ins = 'Options +Indexes';
	}
	if(s === 105){
		ins = 'ErrorDocument 400 /errordocument.html';
	}
	if(s === 106){
		ins = '# (macht aus http://domain.de/xyz.html ein\n# http://domain.de/main.php?xyz)\nRewriteEngine on\nRewriteBase  /\nRewriteRule  ^([a-z]+)\.html$ /main.php?$1 [R,L]';
	}
	if(s === 107){
		ins = 'Deny from IPADRESS\nAllow from IPADRESS';
	}
	if(s === 108){
		ins = 'Redirect /service http://foo2.bar.com/service';
	}
	if(s === 109){
		ins = 'ErrorLog /path/logfile';
	}
	tb.value += '\n' + ins;
}

function WP(s, obj){
	document.getElementById(obj).innerHTML = s;
}

function resizeSQL(i){
	let obj = document.getElementById('sqltextarea');
	let h = 0;
	if(!i){
		obj.style.height = '4px';
	}
	else{
		if(i === 1){
			h = -20;
		}
		if(i === 2){
			h = 20;
		}
		let oh = obj.style.height;
		let s = Number(oh.substring(0, oh.length - 2)) + h;
		if(s < 24){
			s = 24;
		}
		obj.style.height = s + 'px';
	}
}

function InsertLib(i){
	let obj = document.getElementsByName('sqllib')[0];
	if(obj.selectedIndex > 0){
		document.getElementById('sqlstring' + i).value = obj.options[obj.selectedIndex].value;
		document.getElementById('sqlname' + i).value = obj.options[obj.selectedIndex].text;
	}
}

function SelectedTableCount(){
	let obj = document.getElementsByName('f_export_tables[]')[0];
	let anz = 0;
	for(let i = 0; i < obj.options.length; i++){
		if(obj.options[i].selected){
			anz++;
		}
	}
	return anz;
}

function SelectTableList(s){
	let obj = document.getElementsByName('f_export_tables[]')[0];
	for(let i = 0; i < obj.options.length; i++){
		obj.options[i].selected = s;
	}
}

function hide_csvdivs(i){
	document.getElementById('csv0').style.display = 'none';
	if(!i){
		document.getElementById('csv1').style.display = 'none';
		document.getElementById('csv4').style.display = 'none';
		document.getElementById('csv5').style.display = 'none';
	}
}

function check_csvdivs(i){
	hide_csvdivs(i);
	if(document.getElementById('radio_csv0').checked){
		document.getElementById('csv0').style.display = 'block';
	}
	if(!i){
		if(document.getElementById('radio_csv1').checked){
			document.getElementById('csv1').style.display = 'block';
		}
		else if(document.getElementById('radio_csv2').checked){
			document.getElementById('csv1').style.display = 'block';
		}
		else if(document.getElementById('radio_csv4').checked){
			document.getElementById('csv4').style.display = 'block';
		}
		else if(document.getElementById("radio_csv5").checked){
			document.getElementById("csv5").style.display = 'block';
		}
	}
}
