<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\entities\Page;
use Besnovatyj\Page\forms\backend\PageForm;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $page Page */
/* @var $model PageForm */
/* @var $frontendUrl string */
/* @var $absoluteFrontendUrl string */

$this->title = 'Страница: ' . $page->title;
$this->params['breadcrumbs'][] = ['label' => 'Статические страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $page->title, 'url' => ['view', 'id' => $page->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<p>

    <!-- Просмотр на сайте -->
    <a class="btn btn-outline-secondary" target="_blank" href="<?= $absoluteFrontendUrl ?>">
        <i class="bi bi-eye me-1"></i>Просмотр на сайте
    </a>

    <!-- Быстрое добавление в меню -->
    <?= \Besnovatyj\Menu\widgets\add\AddItemWidget::widget([
        'endpoint' => Url::to('/Menu/backend/widget/create', true),
        'link'     => $frontendUrl,
        'name'     => $page->title,
    ]) ?>

    <!-- Доступные шорткоды -->
    <?= \Besnovatyj\Shortcode\widgets\shortcodesList\ShortcodesList::widget([
        'buttonLabel' => 'Доступные шорткоды',
        'buttonClass' => 'btn btn-outline-info',
    ]) ?>

</p>

<div class="page-update">
    <?= $this->render('_form', [
        'model' => $model,
        'page'  => $page,
    ]) ?>
</div>
