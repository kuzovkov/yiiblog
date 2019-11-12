<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sef */

$this->title = 'Create Sef';
$this->params['breadcrumbs'][] = ['label' => 'Sefs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sef-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
