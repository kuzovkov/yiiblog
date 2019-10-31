<?php
$this->title = $post->title;

$this->registerMetaTag([
	'name' => 'description',
	'content' => $post->meta_desc
]);
$this->registerMetaTag([
	'name' => 'keywords',
	'content' => $post->meta_key
])
?>
<div class="post">
	<h1><?php if ($post->is_release) { ?>Выпуск №<?=$post->number?>. <?php } ?><?=$post->title?></h1>
	<hr />
	<table id="fullpost_info">
		<tr>
			<td>
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
			</td>
			<td>
				<?php include "likes.php"; ?>
			</td>
		</tr>
	</table>
	<div class="post_text">
		<img class="intro" src="<?=$post->img?>" alt="<?=$post->title?>" />
		<?=$post->full_text?>

</div>
	<script type="text/javascript">
		VK.Observer.subscribe('widgets.like.liked', function(param) {
			document.getElementById("access_like_1").style.display = "block";
			document.getElementById("access_like_2").style.display = "block";
		});
		window.fbAsyncInit = function() {
			FB.Event.subscribe('edge.create',
				function(response) {
					document.getElementById("access_like_1").style.display = "block";
					document.getElementById("access_like_2").style.display = "block";
				}
			);
		};	
	</script>
	</div>
<hr />
<div id="comments">
	<h2>Добавить комментарий:</h2>
	<div id="comments_vk">
		<div id="vk_comments"></div>
		<script type="text/javascript">
			VK.Widgets.Comments("vk_comments", {limit: 10, width: "496", attach: "graffiti,photo,link"});
		</script>
	</div>
</div>