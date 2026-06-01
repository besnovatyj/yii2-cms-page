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

<?php $this->beginBlock('pageRight.Actions'); ?>
<div class="d-flex flex-column gap-2">

    <!-- Просмотр на сайте -->
    <a class="btn btn-outline-secondary w-100" target="_blank" href="<?= $absoluteFrontendUrl ?>">
        <i class="bi bi-eye me-1"></i>Просмотр на сайте
    </a>

    <!-- Быстрое добавление в меню -->
    <?= \Besnovatyj\Menu\widgets\add\AddItemWidget::widget([
        'endpoint' => Url::to('/Menu/backend/widget/create', true),
        'link'     => $frontendUrl,
        'name'     => $page->title,
    ]) ?>

    <!-- Доступные шорткоды -->
    <div class="card border-info border-opacity-50">
        <div class="card-header py-2 text-info-emphasis small">
            <i class="bi bi-braces me-1"></i>Доступные шорткоды
        </div>
        <div class="card-body p-2">
            <?= \Besnovatyj\Shortcode\widgets\ShortcodesList::widget() ?>
        </div>
    </div>

</div>
<?php $this->endBlock(); ?>

<div class="page-update">
    <?= $this->render('_form', [
        'model' => $model,
        'page'  => $page,
    ]) ?>
</div>
