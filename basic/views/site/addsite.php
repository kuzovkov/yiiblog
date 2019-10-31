<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Добавить сайт";

$this->registerMetaTag([
	'name' => 'description',
	'content' => 'Добавить сайт на блог Михаила Русакова.'
]);
$this->registerMetaTag([
	'name' => 'keywords',
	'content' => 'добавить сайт, добавить сайт блог, добавить сайт блог михаил русаков'
])
?>
<div id="addsite">
	<h2>Заполните форму</h2>
	<?php if ($success) { ?>
		<p class="red">Ваш сайт успешно добавлен!</p>
	<?php } ?>
	<?php if ($error) { ?>
		<p class="red">Произошла ошибка при добавлении! Проверьте данные и повторите попытку.</p>
	<?php } ?>
	<?php $form = ActiveForm::begin(); ?>
		<table>
			<tr>
				<td>Адрес сайта:</td>
				<td>
					<?= $form->field($model, 'address')->label(''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">Описание (не более 255 символов):</td>
			</tr>
			<tr>
				<td colspan="2">
					<?= $form->field($model, 'description')->textarea(['rows' => '6'])->label(''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="hidden" name="func" value="addsite" />
					<table class="button_subscribe">
						<tr>
							<td>
								<input type="image" src="images/button_subscribe_left.png" alt="Добавить сайт" />
							</td>
							<td class="center">
								<?= Html::submitButton('Добавить сайт', ['class' => 'bg_center'])?>
							</td>
							<td>
								<input type="image" src="images/button_subscribe_right.png" alt="Добавить сайт" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<?php ActiveForm::end(); ?>
</div>						