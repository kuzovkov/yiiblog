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
            'did:integer',
            'title',
            [
                'attribute' => 'img',
                'format' => 'raw',
                'value' => function($item){
                    return ($item->img && file_exists($item->relative_images_dir.'/'.basename($item->img)))?
                        sprintf('<img style="max-width: 50px; max-height: 50px;" src="%s"/>', $item->img)
                        :
                        'no image';
                }
            ],
            'default:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
