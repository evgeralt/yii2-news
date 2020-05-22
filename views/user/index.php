<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= Html::beginForm(['send-spam'], 'post'); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'ids',
            ],
            'id',
            'email:email',
            'created_at:datetime',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?= Html::submitButton('Send', ['class' => 'btn btn-primary']); ?>
    <?= Html::endForm(); ?>


</div>
