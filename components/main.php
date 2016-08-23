<?php
if (isset($_POST['parse_value']) && !empty($_POST['parse_value'])) {
	$file = htmlspecialchars(trim($_POST['parse_value']));// обрезаем лишние пробелы и экранируем полученные данные
	$file = (preg_match('%^http(s)?://%', $file)) ? $file : 'http://' . $file;
	$site_name = $file;// название анализируемого сайта
	$file = (preg_match('%/$%', $file)) ? $file . 'robots.txt' : $file . '/robots.txt';// добавление файла robots.txt к каждому анализируемому адресу сайта

	$data['file_size'] = 0;
	$error = '';

	if ($fh = @fopen($file, "r")) {// проверка на существование файла по указаному адресу
		$data['file_exist'] = true;
		$data['search_host'] = 0;
		$data['search_sitemap'] = false;

		$headers = @get_headers($file);
		preg_match("/\d{3}/", $headers[0], $data['headers']);// получение заголовка по файлу

		while(($str = @fread($fh, 1024)) != null) $data['file_size'] += strlen($str);// получение размера файла

		foreach(file($file) as $row) {// поиск и пересчёт директив "Host" и "Sitemap" 
			if (preg_match('/^host:/i', $row)) $data['search_host']++;
			if (preg_match('/^sitemap:/i', $row)) $data['search_sitemap'] = true;
		}
	} else {
		$data['file_exist'] = false;
	}

	header("Content-Type:application/json");
	try {// пробруем подгрузить значения проверок
		if (!@include_once('information.php')) throw new Exception("невозможно загрузить файл с описанием проверок. Настоятельно рекомендуем обратиться к Администратору");
	} catch(Exception $e) {
		die(json_encode(['error' => $e->getMessage()]));// отправка сообщения ошибки
	}

	function buildTable($check_data, $default_text, $success = false) {// шаблон для элементов проверки
		static $number = 0;
		$number++;
		$name_check = $check_data['name_check'];

		if ($success) {
			$class_check = 'success';
			$status_check = $default_text['text_success'];
			$current_state = $check_data['current_state_suc'];
			$recom_check = $default_text['text_check'];
		} else {
			$class_check = 'error';
			$status_check = $default_text['text_error'];
			$current_state = $check_data['current_state_err'];
			$recom_check = $check_data['recom_check_suc'];
		}

		return '<tr class="separate"></tr>' .
		'<tr><td rowspan="2">' . $number . '</td>' .
		'<td rowspan="2">' . $name_check . '</td>' .
		'<td rowspan="2" class="' . $class_check . '">' .$status_check . '</td>' .
		'<td>Состояние</td>' .
		'<td>' . $current_state . '</td></tr>' .
		'<tr><td>Рекомендации</td>' .
		'<td>' . $recom_check . '</td></tr>';
	}

	$result = '';// результаты проверок

	// инициализация проверок
	if (!$data['file_exist']) {// существование файла robots.txt по указаному адресу
		$result .= buildTable($file_exist, $default_text);
	} else {
		$result .= buildTable($file_exist, $default_text, true);

		$result .= ($data['search_host'] > 0) ? buildTable($host_exist, $default_text, true) : buildTable($host_exist, $default_text);// наличие директивы "Host"
		if ($data['search_host'] > 0) $result .= ($data['search_host'] === 1) ? buildTable($host_count, $default_text, true) : buildTable($host_count, $default_text);// количество директив "Host"

		$result .= ($data['file_size'] < (32 * 1024)) ? buildTable($file_size, $default_text, true) : buildTable($file_size, $default_text);// размер файла robots.txt

		$result .= ($data['search_sitemap']) ? buildTable($sitemap, $default_text, true) : buildTable($sitemap, $default_text);// наличие директивы "Sitemap"

		$result .= ((int)$data['headers'][0] === 200) ? buildTable($file_headers, $default_text, true) : buildTable($file_headers, $default_text);// ответ сервера для файла robots.txt
	}
	echo json_encode(['result' => $result, 'siteName' => $site_name]);// отправка данных
}
?>