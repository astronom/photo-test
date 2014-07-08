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

	include_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'Pagination.php');
	$db = require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'db.conf.php');

	$result = mysqli_query($db, 'SELECT * FROM `photos` ORDER by `date` LIMIT 20 OFFSET ' . $page * 20);
	if ($result) {
	$counter = 0;
	while ($data = mysqli_fetch_assoc($result)) {
	?>
	<?php if ($counter % 5 == 0): ?>
	<?php if ($counter > 0): ?>
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

		$photos_count_q = mysqli_query($db, 'SELECT COUNT(`id`) FROM `photos`');
		$total_photos = mysqli_fetch_array($photos_count_q)[0];

		$paging = new Pagination();
		$paging->set('urlscheme','index.php?page=%page%');
		$paging->set('perpage',20);
		$paging->set('page',max(1,intval($_GET['page'])));
		$paging->set('total',$total_photos);
		$paging->set('nexttext','Next Page');
		$paging->set('prevtext','Previous Page');
		$paging->set('focusedclass','selected');
		$paging->set('delimiter','   ');
		$paging->set('numlinks',9);

		?>
		</tr><tr>
			<td colspan="5">
				<?php
					$paging->display();
				?>
			</td>
		</tr>
		<?php
			mysqli_close($db);
		?>

</table>
</body>
</html>


