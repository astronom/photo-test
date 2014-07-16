<?php

require_once realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'db.conf.php');

$db = mysqli_connect('localhost',DB_USER, DB_PASS, 'photo-test');

$data_path = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'data');

// Случайно заполняем теги
for ($i = 0; $i != 100; $i++) {
	$result = mysqli_query($db, "INSERT INTO tags (
			`id` ,
		    `name`)
	    	VALUES (NULL, '" . substr(md5($i), 0, 5) . "')");
}

// Получаем массив созданных id тегов и перемешиваем его
$tags_ids_query = mysqli_query($db, 'SELECT id from tags');
$tags_ids = array();
if ($tags_ids_query) {
	while ($ids = mysqli_fetch_array($tags_ids_query)) {
		$tags_ids[] = $ids[0];
	}

	shuffle($tags_ids);
}

// заполняем таблицу фоток
$row = 1;
if (($handle = fopen($data_path.DIRECTORY_SEPARATOR.'test-photo.csv', 'r')) !== false) {
	while (($data = fgetcsv($handle, 1000, ";")) !== false) {
		$num = count($data);
		if ($num > 0 && $row > 1) {
			$result = mysqli_query($db, "INSERT INTO photos (
				`id` ,
			    `url` ,
			    `date` ,
		    	`user_id` )
		    	VALUES (NULL, '$data[1]', '$data[2]', $data[0])");
			$photo_id = $db->insert_id;

			//	случайно выбираем от 0 до 10 тегов и привязываем их к созданной фотке
			$random_tags_counter = (int)rand(0, 10);
			if ($random_tags_counter > 0) {
				$random = array_rand($tags_ids, $random_tags_counter);

				if(!is_array($random))
					$random = array($random);

				foreach ($random as $key) {
					mysqli_query($db, "INSERT INTO photo_tags (
								`photo_id` ,
							    `tag_id`)
						    	VALUES ($photo_id, $tags_ids[$key])");
				}
			}
		}
		$row++;
	}
	fclose($handle);
}

mysqli_close($db);

echo 'done' . PHP_EOL;