<?php

use app\models\Author;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\AuthorSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Authors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= !Yii::$app->user->isGuest ? Html::a('Create Author', ['create'], ['class' => 'btn btn-success']) : null ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($data) {
                    return '<a href="' . Url::to(['author/view', 'id' => $data->id]) . '" class="btn-link">' . $data->name . '</a>';
                }
            ],
            [
                'attribute' => 'books',
                'format' => 'raw',
                'value' => function($data) {
                    return count($data->getRelation('books')->all());
                }
            ],
        ],
    ]); ?>


</div>
