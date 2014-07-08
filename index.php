<html>
<head>
	<title>Тестовое задание</title>
</head>
<body>
<table>
	<?php
	/**
	 * Created by PhpStorm.
	 * User: astronom
	 * Date: 08.07.14
	 * Time: 13:41
	 */
	if (!empty($_GET['page']))
		$page = (int)$_GET['page'];
	else
		$page = 0;

	$db = require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'db.conf.php');

	$result = mysqli_query($db, 'SELECT * FROM `photos` ORDER by `date` LIMIT 20 OFFSET ' . $page * 20);
	if ($result) {
		$counter = 0;
		while ($data = mysqli_fetch_assoc($result)) {
			?>
			<?php if ($counter % 5 == 0): ?>
				<?php if($counter > 0): ?>
				</tr>
				<?php endif ?>
				<tr>
			<?php endif ?>
			<td>
				<img src="<?php echo $data['url'] ?>">
				<span><?php echo $data['date'] ?></span>
			</td>
			<?php
			$counter++;
		}
	}
	mysqli_close($db);

	?>

</table>
</body>
</html>


