function getXmlHttp(){// фц-я получения обьекта XHR
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

var address = document.getElementById('parse_value');
var form = document.getElementById('parse_site');
var button = document.getElementById('parse_start');
var info = document.getElementsByClassName('load')[0];

var table = document.getElementById('result-table');
var tableBody = table.getElementsByTagName('tbody')[0];


form.onsubmit = function() {// инициализация аналализа
	var error = this.getElementsByClassName('errorInfo')[0];
	error.innerHTML = '';
	if (address.value.length === 0) {
		error.innerHTML = 'Пожалуйста, введите адрес сайта';
		return false;
	}
	if (!address.value.match(/[\w-_\.]*(\w+\.[a-z]{2,}\/?)$/i)) {
		error.innerHTML = 'Некорректный адрес сайта';
		return false;
	}

	table.style.display = 'none';
	info.style.display = 'block';
	var data = "parse_value=" + encodeURIComponent(document.getElementById('parse_value').value);
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
	tableBody.innerHTML = data;
	info.style.display = 'none';
	table.style.display = 'table';
}


