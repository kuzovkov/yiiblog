<?php
$this->title = "Видеоотзывы";

$this->registerMetaTag([
	'name' => 'description',
	'content' => 'Видеоотзывы о Русакове Михаиле Юрьевиче и его видеокурсах.'
]);
$this->registerMetaTag([
	'name' => 'keywords',
	'content' => 'михаил русаков отзывы, курсы михаила русакова отзывы, курсы михаила русакова видеоотзывы'
])
?>
<div id="custom">
	<h2>Видеоотзывы</h2>
	<hr />
	<?php include "likes.php"; ?>
	<div class="post_text">
		<p class="center">
			<img src="/images/reviews.png" alt="Видеоотзывы" />
		</p>
		<p>В последнее время в Интернете стало очень много самых различных учителей по созданию сайтов. И, на мой взгляд, самый важный критерий качества материалов того или иного учителя - это отзывы его учеников.</p>
		<p>По моему мнению, они должны быть в обязательном порядке либо в социальных сетях, где виден отзыв и виден аккаунт этого человека. Такие отзывы у меня есть тут: <a href="http://vk.com/myrusakov">http://vk.com/myrusakov</a> и тут <a href="http://vk.com/rusakovmy">http://vk.com/rusakovmy</a>.</p>
		<p>Но ещё показательнее, на мой взгляд, видеоотзывы, с которыми Вы уже можете ознакомиться на этой странице.</p>
		<p>Если Вы хотите тоже записать видеоотзыв, то записывайте, выкладывайте его на YouTube или в контакт, и присылайте ссылку на него в мою службу поддержки: <a href="http://support.myrusakov.ru">http://support.myrusakov.ru</a>. Крайне желательно, в самом видео рассказать о сайтах, которые Вы создали благодаря моим курсам. Также можете рассказать истории о своих заработках благодаря умению создавать сайты.</p>
		<?php foreach ($reviews as $review) { ?>
			<h2>Отзыв от <?=$review->from?></h2>
			<?=$review->video?>
			<br />
		<?php } ?>

	</div>
</div>		