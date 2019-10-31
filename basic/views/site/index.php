<?php
use yii\widgets\LinkPager;

$this->title = "Личный блог Михаила Русакова";

$this->registerMetaTag([
	'name' => 'description',
	'content' => 'Личный блог Михаила Русакова и его выпуски рассылки.'
]);
$this->registerMetaTag([
	'name' => 'keywords',
	'content' => 'михаил русаков, блог михаил русаков, рассылка михаил русаков'
])

?>
<?php
	foreach ($posts as $post)
		include "intro_post.php";
?>
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