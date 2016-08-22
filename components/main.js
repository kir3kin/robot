function getXmlHttp(){
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

var form = document.getElementById('parse_site');
var button = document.getElementById('parse_start');
var table = document.getElementById('result-table');


button.onclick = function() {
	var data = "parse_value=" + encodeURIComponent(form.getElementsByClassName('parse_value')[0].value);
	var xmlhttp = getXmlHttp()
	xmlhttp.open('POST', 'components/main.php', true);
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlhttp.send(data);

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4) {
			if(xmlhttp.status === 200) {
				checkData(xmlhttp.responseText);
			}
		}
	};
	return false;
};

function checkData(data) {
	table.innerHTML = data;
}


