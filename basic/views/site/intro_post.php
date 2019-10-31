<div class="post">
	<h2><?php if ($post->is_release) { ?>Выпуск №<?=$post->number?>. <?php } ?><?=$post->title?></h2>
	<hr />
	<table class="post_info">
		<tr>
			<td>
				<img src="/web/images/date.png" alt="Дата" />
			</td>
			<td>
				<p><?=$post->date?></p>
			</td>
			<td class="right">
				<img src="/web/images/hits.png" alt="Просмотров" />
			</td>
			<td class="center">
				<p><?=$post->hits?></p>
			</td>
		</tr>
	</table>
	<div class="post_text">
		<img class="intro" src="<?=$post->img?>" alt="<?=$post->title?>" />
		<?=$post->intro_text?>
		<div class="clear"></div>
	</div>
	<p class="more">
		<a href="<?=$post->link?>">Читать полностью</a>
		<a href="<?=Yii::$app->urlManager->createUrl(["site/releases"])?>">Другие выпуски</a>
	</p>
</div>