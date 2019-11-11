<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Minicourses */

$this->title = 'Create Minicourses';
$this->params['breadcrumbs'][] = ['label' => 'Minicourses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="minicourses-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
