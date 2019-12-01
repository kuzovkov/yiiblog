<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** install: composer require 2amigos/yii2-ckeditor-widget **/
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\models\Sites */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sites-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['disabled' => 'disabled']) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic',
        'kcfinder' => true,
        'kcfOptions' => [
            'uploadURL' => '@web/upload',
            'uploadDir' => '@webroot/upload',
            'access' => [  // @link http://kcfinder.sunhater.com/install#_access
                'files' => [
                    'upload' => true,
                    'delete' => true,
                    'copy' => true,
                    'move' => true,
                    'rename' => true,
                ],
                'dirs' => [
                    'create' => true,
                    'delete' => true,
                    'rename' => true,
                ],
            ],
            'types' => [  // @link http://kcfinder.sunhater.com/install#_types
                'files' => [
                    'type' => '',
                ],
            ],
        ]
    ]); ?>

    <?= $form->field($model, 'active')->checkbox(['value' => true, 'label' => 'Is active']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
