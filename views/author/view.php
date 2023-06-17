<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Author $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Authors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php Modal::begin([
            'title' => 'Enter phone',
            'toggleButton' => [
                'label' => 'Subscribe',
                'class' => 'btn btn-primary',
                'data-toggle' => 'modal',
                'data-target' => '#subscribe-modal'
            ],
        ]);

            $form = ActiveForm::begin([
                'action' => Url::to(['author/subscribe', 'id' => $model->id]),
                'method' => 'get',
            ]);

            echo $form->field($guest, 'phone')->textInput(['maxlength' => true]); ?>
            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php Modal::end(); ?>

        <?php if (!Yii::$app->user->isGuest) { ?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'books',
                'format' => 'raw',
                'value' => function($data) {
                    $result = [];
                    $books = $data->getRelation('books')->all();
                    $booksByYear = $data->getBooksByYear();

                    return $list = Html::ul($booksByYear, ['item' => function($item, $index) {
                        return Html::tag(
                            'li',
                            "<span>$index</span>" . Html::ul($item, ['item' => function($item, $index) {
                                return Html::tag(
                                    'li',
                                    '<a href="' . Url::to(['book/view', 'id' => $item['id']]) . '">' . $item['name'] . '</a>',
                                );
                            }])
                        );
                    }]);
                }
            ],
        ],
    ]) ?>

</div>
