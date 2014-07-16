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
			foreach ($userCrossTagsList as $tag)
				echo '<a title="Убрать тег" href="/crossTag/remove/' . $tag->id . '">' . $tag->name . '</a>   ';
			?>
		</td>
	</tr>
	<tr>
		<td colspan="5">Исключение тегов:
			<?php
			foreach ($userMissedTagsList as $tag)
				echo '<a title="Убрать тег" href="/missedTag/remove/' . $tag->id . '">' . $tag->name . '</a>   ';
			?>
		</td>
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
			<br>
			<table>
				<tr>
					<td>
						<?php
						foreach ($photo->getTags() as $tag) {
							if (in_array($tag->id, $userCrossTags))
								echo '<a style="color: green" href="/crossTag/add/' . $tag->id . '">' . $tag->name . '</a><br>';
							else
								echo '<a href="/crossTag/add/' . $tag->id . '">' . $tag->name . '</a><br>';
						}
						?>
					</td>
					<td>
						<?php
						foreach ($photo->getTags() as $tag) {
							if (in_array($tag->id, $userMissedTags))
								echo '<a style="color: red" href="/missedTag/add/' . $tag->id . '">' . $tag->name . '</a><br>';
							else
								echo '<a href="/missedTag/add/' . $tag->id . '">' . $tag->name . '</a><br>';
						}
						?>
					</td>
				</tr>
			</table>
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
