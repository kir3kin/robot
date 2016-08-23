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

var state = false;// переменная для фц-и задержки
var form = document.getElementById('parse_site');// форма отправки адреса сайта
form.addEventListener('submit', checkForm);

function checkForm(e) {// инициализация анализа
	e.preventDefault();// отмена привычной отправки формы
	var address = this.getElementsByClassName('parse_value')[0];// введённый адрес сайта
	var pattern = new RegExp(/(https?:\/\/)?([\w-_]+\.[\w-_\/]+)+/, 'i');// регульрное выражение вида 
	var error = this.getElementsByClassName('errorInfo')[0];// поле вывода ошибок

	// проверки введённых данных
	if (address.value.length === 0) {
		error.innerHTML = 'Пожалуйста, введите адрес сайта';
		return false;
	}
	if (!pattern.test(address.value)) {
		error.innerHTML = 'Некорректный адрес сайта';
		return false;
	}
	if (delay()) return false;// инициализация задержки на время анализа

	var resultTable = document.getElementById('result');// div c таблицей результата анализа сайта
	var tableBody = resultTable.getElementsByTagName('tbody')[0];// тело таблици для ввода результата проверок
	var resultHeader = resultTable.getElementsByClassName('site-name')[0];// span для ввода имени анализируемого сайта
	var button = this.getElementsByClassName('start-analysis')[0];// кнопка отправки данных
	var info = document.getElementsByClassName('load')[0];// сообщение об начале анализа


	// последние изменения перед отправкой
	resultTable.style.display = 'none';// ищезание таблици с результатами перед каждым следуйщим анализом
	info.style.display = 'block';// появление сообщения об начале анализа
	button.style.opacity = ".5";
	error.innerHTML = '';// очищаем поле с ошибками

	// подготовка к отправке данных
	var data = "parse_value=" + encodeURIComponent(address.value);// кодируем введённый пользователем, адрес сайта
	var xmlhttp = getXmlHttp();
	xmlhttp.open('POST', 'components/main.php', true);
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlhttp.send(data);

	xmlhttp.onreadystatechange = function() {// ожидание ответа
		if (xmlhttp.readyState === 4) {
			if(xmlhttp.status === 200) {
				checkData(xmlhttp.responseText);
			}
		}
	};

	function checkData(data) {// подстановка полученных данных и появление таблици с результатом
		data = JSON.parse(data);
		if (data.error) {
			error.innerHTML = 'При анализе указанного сайта возникла ошибка: ' + data.error + '!';
		} else {
			tableBody.innerHTML = data.result;
			resultHeader.innerText = data.siteName;
			resultTable.style.display = 'table';
		}
		info.style.display = 'none';
		button.style.opacity = "1";
		state = false;
	}

	function delay() {
		if (state) return true;
		state = true;
	}
}