<div class="form_subscribe">
	<h3>Заполните форму</h3>
	<form name="subscribe_<?=$course->id?>" action="http://srs.myrusakov.ru/subscribe" method="post" onsubmit="return SR_submit(this)">
		<p>
			<input type="text" class="input" name="name" value="Ваше имя" onfocus="if(this.value=='Ваше имя') this.value='';" onblur="if(this.value=='') this.value='Ваше имя'" />
		</p>
		<p>
			<input type="text" class="input" name="email" value="Ваш e-mail адрес" onfocus="if(this.value=='Ваш e-mail адрес') this.value=''" onblur="if(this.value=='') this.value='Ваш e-mail адрес'" />
		</p>
		<table class="button_subscribe">
			<tr>
				<td>
					<input type="hidden" name="delivery_id" value="<?=$course->did?>" />
					<input type="image" src="/web/images/button_subscribe_left.png" alt="Получить Видеокурс" />
				</td>
				<td class="center">
					<input type="submit" class="bg_center" value="Получить Видеокурс" />
				</td>
				<td>
					<input type="image" src="/web/images/button_subscribe_right.png" alt="Получить Видеокурс" />
				</td>
			</tr>
		</table>
	</form>
</div>