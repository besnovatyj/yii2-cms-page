<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\forms\backend\PageForm;
use yii\web\View;

/* @var $this View */
/* @var $model PageForm */

$this->title = 'Создать страницу';
$this->params['breadcrumbs'][] = ['label' => 'Статические страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
