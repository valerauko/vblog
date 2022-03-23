// /*
// vBlog 2.0 AJAX search script
// copyright 2006-2007 Vale
// */

var xmlHttp;

function fieldBlur() {
	var field = document.getElementById('search_phrase');
	if(field.value=='') {
		field.value='search...';
		document.getElementById('search_result').style.display = "none";
		document.getElementById('search_result').innerHTML = "";
	}
}
function fieldFocus() {
	var field = document.getElementById('search_phrase');
	if(field.value=='search...') {
		field.value='';
	}
}
function initAjax() {
	if (window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) { return false; }
		}
	}
	return xmlHttp;
}
function liveSearch(dir) {
	var str = document.getElementById("search_phrase").value;
	document.getElementById('search_result').style.display = "block";
	document.getElementById('search_result').innerHTML = "";

	if(str.length < 4) {
		document.getElementById("search_result").innerHTML = "<li>Enter "+(4-str.length)+" more characters to start searching.</li>";
	} else {
		document.getElementById("search_result").innerHTML = "";
		xmlHttp = initAjax();
		if(xmlHttp == null) {
			document.getElementById("search_result").innerHTML = "<li>Sorry, your browser does not support this search method.</li>";
		} else {
			var url = "http://blog.valerauko.net/script/live.php";
			xmlHttp.onreadystatechange = stateChange;
			xmlHttp.open("POST",url,true);
			xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");
			xmlHttp.setRequestHeader("Content-Encoding","UTF-8");
			xmlHttp.send("search_string="+str);
			document.getElementById("search_result").innerHTML = "<li><img src=\"http://blog.valerauko.net/layout/"+dir+"/loading.gif\" alt=\"Loading...\" class=\"loading\" /> Search in progress...</li>";
		}
	}
}
function stateChange() {
	if(xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
		document.getElementById('search_result').style.display = "block";
		if(xmlHttp.status == 200) {
			document.getElementById("search_result").innerHTML = xmlHttp.responseText;
		} else {
			document.getElementById("search_result").innerHTML = "<li>Sorry, unexpected problems prevented your search from finishing.</li>";
		}
	}
}