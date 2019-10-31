<?php
use yii\widgets\LinkPager;

$this->title = "Поиск $q";

$this->registerMetaTag([
	'name' => 'description',
	'content' => "Поиск: $q."
]);
$this->registerMetaTag([
	'name' => 'keywords',
	'content' => $q
])
?>
<?php if ($q == "") { ?>
	<h2>Вы задали пустой поисковый запрос!</h2>
<?php } else { ?>
	<h2>Результаты поиска: <?=$q?></h2>
	<?php if (!$posts) { ?>
		<p>Ничего не найдено</p>
	<?php } else { ?>
		<?php foreach ($posts as $post) include "intro_post.php"; ?>
			<br />
			<hr />
			<div id="pages">
				<?= LinkPager::widget([
					'pagination' => $pagination,
					'firstPageLabel' => 'В начало',
					'lastPageLabel' => 'В конец',
					'prevPageLabel' => '&laquo;'
				]) ?>
				<span>Страница <?=$active_page?> из <?=$count_pages?></span>
				<div class="clear"></div>
			</div>
	<?php } ?>
<?php } ?>