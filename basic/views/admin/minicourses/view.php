<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Minicourses */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Minicourses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="minicourses-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
        ],
    ]) ?>

</div>
