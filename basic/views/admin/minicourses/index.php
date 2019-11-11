<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MinicoursesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Minicourses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="minicourses-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Minicourses', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'did',
            'title',
            'img',
            'default',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
