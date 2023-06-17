<?php

use app\models\Author;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */

$this->title = 'Top authors by ' . $year . ' year';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <h2>Years</h2>
            <?= Html::ul($years, ['item' => function($item, $index) {
                return Html::tag(
                    'li',
                    '<a href="' . Url::to(['site/top', 'year' => $item]) . '">' . $item . '</a>',
                );
            }]) ?>
        </div>

        <div class="col-md-6">
            <h2>Top Authors</h2>
            <?= Html::ul($authors, ['item' => function($item, $index) {
                return Html::tag(
                    'li',
                    '<a href="' . Url::to(['author/view', 'id' => $item['author']['id']]) . '">' . $item['author']['name'] . ' (' . $item['book_count'] . ' books)' . '</a>',
                );
            }]) ?>
        </div>
    </div>

</div>
