<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Subscribe';

$this->params['breadcrumbs'][] = ['label' => 'Authors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-subscribe">
    <?= $error ?>
    <?= $message ?>
</div>
