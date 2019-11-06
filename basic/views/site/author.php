<?php
$this->title = "Михаил Русаков";

$this->registerMetaTag([
	'name' => 'description',
	'content' => 'Об авторе блога Русакове Михаиле Юрьевиче.'
]);
$this->registerMetaTag([
	'name' => 'keywords',
	'content' => 'об авторе, михаил русаков, михаил юрьевич русаков'
])
?>

<div id="custom">
	<h2>Об авторе</h2>
	<hr />
		<?php include "likes.php"; ?>
	<div class="post_text">
		<p class="center">
			<img src="/images/author.png" alt="Об авторе" />
		</p>
		<p>Спасибо, что захотели поинтересоваться автором этого блога! Меня зовут <b>Русаков Михаил Юрьевич</b>.</p>
		<p>Для тех, кому интересно, как я вообще начинал (а начинал я абсолютно с нуля, как и многие из Вас, думаю), посмотрите это видео, которое так и называется: <a rel="external" href="<?=Yii::$app->urlManager->createUrl(['site/post', 'id' => 28])?>">Моя история</a>.</p>
		<p>Сейчас я достаточно успешный и известный программист и Web-мастер. У меня уже <b>более 1500 клиентов со всего мира</b>, мой сайт <a rel="external" href="http://myrusakov.ru">MyRusakov.ru</a> <b>ежедневно посещают тысячи человек</b>. У меня масса постоянных заказчиков и несколько обучающих Видеокурсов. У меня множество довольных учеников и клиентов. Отзывы некоторых из них Вы можете почитать у меня на <a rel="external" href="http://vk.com/myrusakov">стене вконтакте</a> (до 19-го апреля 2014 года) и в <a rel="external" href="http://vk.com/rusakovmy">моей группе</a> (от 19-го апреля 2014 года). Я <b>полностью себя обеспечиваю, и не работаю ни в каком офисе</b>. Работаю исключительно дома (иногда с ноутбуком и в машине могу поработать), причём <b>тогда, когда захочу и сколько захочу</b>.</p>
		<p>Но не похвалиться я хотел, а рассказать, что <b>любой человек может стать таким же независимым</b>. Теперь немного о себе.</p>
		<p>Я родился <b>11-го июля 1990-го года</b> в городе Обнинске, Калужской области. С <b>10-го класса</b> я сильно увлёкся программированием, поэтому сразу для себя решил, кем я буду.</p>
		<p>В <b>2007-м году</b> я поступил на бюджетное отделение факультета "<b>Автоматики и вычислительной техники</b>" Московского Энергетического Института. На данный момент я на <b>6-ом курсе</b> и уже летом 2013-го года выйду из института со степенью Магистра.</p>
		<p>В <b>2009-м году</b> я решил <b>помогать начинающим Web-мастерам создавать сайты</b>. Так был создан сайт <a rel="external" href="http://myrusakov.ru">MyRusakov.ru</a>, на котором я регулярно и по сей день добавляю свои новые статьи по теме <b>создания и раскрутки сайтов</b>. Также у меня есть обучающие Видеокурсы, посвящённые той же тематике. Полный список Вы можете посмотреть на <a href="<?=Yii::$app->urlManager->createUrl(['site/video'])?>">этой</a> странице.</p>
		<p>Что касается этого блога, то на нём я буду публиковать:</p>
		<ul>
			<li><b>Выпуски своей рассылки</b>, посвящённые созданию и раскрутке сайтов.</li>
			<li><b>Посты о личной эффективности</b>. Поскольку кем бы Вы ни были, будь-то программистом, будь-то юристом, или менеджером по продажам в автосалоне. Везде можно быть успешным человеком и фаворитом. И вот на этом блоге я буду публиковать материалы по тому, как и стать этим успешным человеком. Причём именно то, что я сам использую и что мне действительно помогает.</li>
			<li><b>Небольшие истории из моей жизни</b>.</li>
		</ul>
		<p>Надеюсь, Вам понравится мой блог. Спасибо, что уделили мне время!</p>
		<p>
			<i>С Уважением, Михаил Русаков!</i>
		</p>
	</div>
</div>