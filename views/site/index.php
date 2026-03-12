<?php

use app\models\Categories;

/** @var yii\web\View $this */

$this->title = 'TaskForce';
$cats = new Categories()->find()->all();
?>
<div>
    <h1>Hello World</h1>
    <?php foreach ($cats as $cat) : ?>
        <p><?= $cat->name ?></p>
    <?php endforeach ?>
</div>
