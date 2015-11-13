<?php

if ($_POST['placeaction'] == "tile" && isset($_POST['image']) && isset($_POST['watermark'])) {

	//Определяем папку для загрузки файлов
	$path = "files";
	$pathtosave = "files/watermarked";
	//Вроде файлы есть, плэйсим их в переменные
	$image = $_POST['image'];
	$waterimage = $_POST['watermark'];

	//Если таковой нет в дереве, запиливаем немедленно
	if (!file_exists($pathtosave)) {
		mkdir($pathtosave);
	}

	//Проверяем основное изображение и делаем нужное сжатие
	if (preg_match('/[.](GIF)|(gif)$/', $image)) {
		$im = imagecreatefromgif($path . "/" . $image);
	}
	else if (preg_match('/[.](PNG)|(png)$/', $image)) {
		$im = imagecreatefrompng($path . "" . $image);
	}

	else if (preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $image)) {
		$im = imagecreatefromjpeg($path . "/" . $image);
	}

	//Проверяем вотермарк по типу и делаем нужное сжатие
	if (preg_match('/[.](GIF)|(gif)$/', $waterimage)) {
		$pattern = imagecreatefromgif($path . "/" . $waterimage);
	}
	else if (preg_match('/[.](PNG)|(png)$/', $waterimage)) {
		$pattern = imagecreatefrompng($path . "" . $waterimage);
	}
	else if (preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $waterimage)) {
		$pattern = imagecreatefromjpeg($path . "/" . $waterimage);
	}

	$imWidth = imagesx($im);
	$imHeight = imagesy($im);

	// 4. Create background pattern from image

	$patternWidth = imagesx($pattern);
	$patternHeight = imagesy($pattern);

	// 5. Repeatedly copy pattern to fill target image
	if ($patternWidth < $imWidth || $patternHeight < $imHeight) {
		for ($patternX = 0; $patternX < $imWidth; $patternX += $patternWidth) {
			for ($patternY = 0; $patternY < $imHeight; $patternY += $patternHeight) {
				imagecopymerge($im, $pattern, $patternX, $patternY, 30, 30, $patternWidth, $patternHeight, 60);
			}
		}

		imagejpeg($im, 'spazm.jpg');
	}

	else {
		imagecopymerge($im, $pattern, 0, 0, 0, 0, $patternWidth, $patternHeight, 60);
		imagejpeg($im, 'spazm.jpg');
	}

	imagejpeg($im, 'spazm.jpg');

	//А вот и сама функция
    function file_force_download($file) {
		if (file_exists($file)) {
		// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
		// если этого не сделать файл будет читаться в память полностью!
		if (ob_get_level()) {
		  ob_end_clean();
	}
		// Возвращаем файл и заголовки. XMLHttpResponse на клиенте выполнит сохранение файла
		header('Content-Description: File Transfer');
		header('Content-Type: image/jpeg');
		header('Content-Disposition: attachment; filename=' . basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		// читаем файл и отправляем его пользователю
		if ($fd = fopen($file, 'rb')) {
			while (!feof($fd)) {
				print fread($fd, 1024);
			}
			fclose($fd);
		}
			exit;
		}
	}

	//Модная функция пушинга файла в браузер
	file_force_download($im);

}
else {
	header('Content-Disposition: attachment; filename=NOFILE.php');
	header("HTTP/1.0 404 Not Found");
	exit('NO FILES');
}

?>