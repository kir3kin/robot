<?php
	// default
	$default_text = [
		'text_check' => 'Доработки не требуются',
		'text_success' => 'Ок',
		'text_error' => 'Ошибка',
	];

	// file exist
	$file_exist = [
		'name_check' => 'Проверка наличия файла robots.txt',
		'recom_check_suc' => 'Создать файл robots.txt и разместить его на сайте',
		'current_state_suc' => 'Файл robots.txt присутствует',
		'current_state_err' => 'Файл robots.txt отсутствует',
	];

	// host exist
	$host_exist = [
		'name_check' => 'Проверка указания директивы Host',
		'recom_check_suc' => 'Необходимо добавить в файл robots.txt директиву Host',
		'current_state_suc' => 'Директива Host указана',
		'current_state_err' => 'В файле robots.txt не указана директива Host',
	];

	// host count
	$host_count = [
		'name_check' => 'Проверка количества директив Host',
		'recom_check_suc' => 'Директива Host должна быть указана в файле robots.txt только 1 раз',
		'current_state_suc' => 'В файле robots.txt прописана 1 директива Host',
		'current_state_err' => 'В файле robots.txt прописано несколько директив Host',
	];

	// file size
	$file_size = [
		'name_check' => 'Проверка размера файла robots.txt',
		'recom_check_suc' => 'Размер файла robots.txt не должен привышать 32 кб',
		'current_state_suc' => 'Размер файла robots.txt составляет ' . $data['file_size'] . ' байт, что находится в пределах допустимой нормы',
		'current_state_err' => 'Размер файла robots.txt составляет ' . $data['file_size'] . ' байт, что превышает допустимую норму',
	];

	// sitemap
	$sitemap = [
		'name_check' => 'Проверка указания директивы Sitemap',
		'recom_check_suc' => 'Добавить в файл robots.txt директиву Sitemap',
		'current_state_suc' => 'Директива Sitemap указана',
		'current_state_err' => 'В файле robots.txt не указана директива Sitemap',
	];

	// headers
	$file_headers = [
		'name_check' => 'Проверка кода ответа сервера для файла robots.txt',
		'recom_check_suc' => 'Файл robots.txt должен отдавать код ответа 200, иначе файл не будет обрабатываться.',
		'current_state_suc' => 'Файл robots.txt отдаёт код ответа 200',
		'current_state_err' => 'При обращении к файлу robots.txt сервер возвращает код ответа ' . $data['headers'][0],
	];
?>