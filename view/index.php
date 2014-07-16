<html>
<head>
	<title>Тестовое задание</title>
</head>
<body>
<table>
	<tr>
		<td colspan="5">
			Пересечение тегов:
			<?php
			foreach ($userTagsList as $tag)
				echo '<a title="Убрать тег" href="/crossTag/remove/' . $tag->id . '">' . $tag->name . '</a>   ';
			?>
		</td>
	</tr>
	<tr>
		<td colspan="5">Исключение тегов:</td>
	</tr>
	<tr>
		<td colspan="5">Сортировка:</td>
	</tr>
	<?php
	$counter = 0;

	foreach ($photoList as $photo):
	?>
	<?php if ($counter % 5 == 0): ?>
	<?php if ($counter > 0): ?>
		</tr>
	<?php endif ?>
	<tr>
		<?php endif ?>

		<td>
			<img src="<?php echo $photo->url ?>">
			<br>
			<span><?php echo $photo->date ?></span>
			<?php
			foreach ($photo->getTags() as $tag) {
				if (in_array($tag->id, $userTags))
					echo '<a style="color: red" href="/crossTag/add/' . $tag->id . '">' . $tag->name . '</a><br>';
				else
					echo '<a href="/crossTag/add/' . $tag->id . '">' . $tag->name . '</a><br>';
			}
			?>

		</td>
		<?php $counter++; ?>
		<?php endforeach; ?>
	</tr>
	<tr>
		<td colspan="5">
			<?php
			$paging->display();
			?>
		</td>
	</tr>
</table>
</body>
</html>
