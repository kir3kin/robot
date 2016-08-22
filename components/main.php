<?php
if (isset($_POST['parse_value'])) $file = (!empty($_POST['parse_value'])) ? $_POST['parse_value'] : '';
if (!empty($file)) {
	$file = (preg_match('%^http(s)?://%', $file) )? $file : 'http://' . $file;
	$file = preg_match('%/$%', $file) ? $file . 'robots.txt' : $file . '/robots.txt';

	$data['file_size'] = 0;

	if ($fh = @fopen($file, "r")) {
		$data['file_exist'] = true;
		$data['search_host'] = 0;
		$data['search_sitemap'] = false;

		$headers = @get_headers($file);
		preg_match("/\d{3}/", $headers[0], $data['headers']);

		while(($str = @fread($fh, 1024)) != null) $data['file_size'] += strlen($str);

		foreach(file($file) as $row) {
			if (preg_match('/^host:/i', $row)) $data['search_host']++;
			if (preg_match('/^sitemap:/i', $row)) $data['search_sitemap'] = true;
		}
	} else {
		$data['file_exist'] = false;
	}


	function buildTable($name_check, $status_check, $current_state, $recom_check) { ?>
	<tr>
		<td rowspan="2"><?= $name_check ?></td>
		<td rowspan="2" class="<?= ($status_check === "Ок") ? 'success' : 'error' ?>"><?= $status_check ?></td>
		<td>Состояние</td>
		<td><?= $current_state ?></td>
	</tr>
	<tr>
		<td>Рекомендации</td>
		<td><?= $recom_check ?></td>
	</tr>
	<?php }

	$default['recom_check'] = 'Доработки не требуются';
	$default['suc'] = "Ок";
	$default['err'] = "Ошибка";
	// exist
	$file_exist['name_check'] = 'Проверка наличия файла robots.txt';
	$file_exist['true']['current_state'] = 'Файл robots.txt присутствует';
	$file_exist['false']['current_state'] = 'Файл robots.txt отсутствует';
	$file_exist['false']['recom_check'] = 'Создать файл robots.txt и разместить его на сайте';

	// headers
	$file_headers['name_check'] = 'Проверка кода ответа сервера для файла robots.txt';
	$file_headers['true']['current_state'] = 'Файл robots.txt отдаёт код ответа 200';
	$file_headers['false']['current_state'] = 'При обращении к Файлу robots.txt сервер возвращает код ответа ' . $data['headers'];
	$file_headers['false']['recom_check'] = 'Файл robots.txt долже отдавать код ответа 200, иначе файл не будет обрабатываться.';

	// host exist
	$host_exist['name_check'] = 'Проверка указания директивы Host';
	$host_exist['true']['current_state'] = 'Директива Host указана';
	$host_exist['false']['current_state'] = 'В файле robots.txt не указана директива Host ';
	$host_exist['false']['recom_check'] = 'Необходимо добавить в файл robots.txt директиву Host';

	// host count
	$host_count['name_check'] = 'Проверка количества директив Host';
	$host_count['true']['current_state'] = 'В файле robots.txt прописана 1 директива Host';
	$host_count['false']['current_state'] = 'В файле robots.txt прописано несколько директив Host';
	$host_count['false']['recom_check'] = 'Директива Host должна быть указана в файле robots.txt только 1 раз';

	// sitemap
	$sitemap['name_check'] = 'Проверка указания директивы Sitemap';
	$sitemap['true']['current_state'] = 'Директива Sitemap указана';
	$sitemap['false']['current_state'] = 'В файле robots.txt не указана директива Sitemap';
	$sitemap['false']['recom_check'] = 'Добавить в файл robots.txt директиву Sitemap';

	// file size
	$file_size['name_check'] = 'Проверка размера файла robots.txt';
	$file_size['true']['current_state'] = 'Размер файла robots.txt составляет ' . $data['file_size'] . ' байт, что находится в пределах допустимой нормы';
	$file_size['false']['current_state'] = 'Размер файла robots.txt составляет ' . $data['file_size'] . ' байт, что превышает допустимую норму';
	$file_size['false']['recom_check'] = 'Размер файла robots.txt не должен привышать 32 кб';


	$result = '';

	if (!$data['file_exist']) {
		$result .= buildTable($file_exist['name_check'], $default['err'], $file_exist['false']['current_state'], $file_exist['false']['recom_check']);
	} else {
		$result .= buildTable($file_exist['name_check'], $default['suc'], $file_exist['true']['current_state'], $default['recom_check']);

		$result .= ($data['search_host'] > 0) ? buildTable($host_exist['name_check'], $default['suc'], $host_exist['true']['current_state'], $default['recom_check']) : buildTable($host_exist['name_check'], $default['err'], $host_exist['false']['current_state'], $host_exist['false']['recom_check']);

		if ($data['search_host'] > 0) {
			$result .= ($data['search_host'] === 1) ? buildTable($host_count['name_check'], $default['suc'], $host_count['true']['current_state'], $default['recom_check']) : buildTable($host_count['name_check'], $default['err'], $host_count['false']['current_state'], $host_count['false']['recom_check']);
		}

		$result .= ($data['file_size'] < (32 * 1024)) ? buildTable($file_size['name_check'], $default['suc'], $file_size['true']['current_state'], $default['recom_check']) : buildTable($file_size['name_check'], $default['err'], $file_size['false']['current_state'], $file_size['false']['recom_check']);

		$result .= ($data['search_sitemap']) ? buildTable($sitemap['name_check'], $default['suc'], $sitemap['true']['current_state'], $default['recom_check']) : buildTable($sitemap['name_check'], $default['err'], $sitemap['false']['current_state'], $sitemap['false']['recom_check']);

		$result .= ((int)$data['headers'][0] === 200) ? buildTable($file_headers['name_check'], $default['suc'], $file_headers['true']['current_state'], $default['recom_check']) : buildTable($file_headers['name_check'], $default['err'], $file_headers['false']['current_state'], $file_headers['false']['recom_check']);
	}

	echo $result;
}
?>