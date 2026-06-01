<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\entities\Page;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $page Page */
/* @var $absoluteFrontendUrl string */
/* @var $frontendUrl string */

$this->title = $page->title;
$this->params['breadcrumbs'][] = ['label' => 'Статические страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusLabel = match ($page->status) {
    Page::STATUS_PUBLISHED => '<span class="badge bg-success">Опубликована</span>',
    Page::STATUS_DRAFT     => '<span class="badge bg-secondary">Черновик</span>',
    Page::STATUS_ARCHIVED  => '<span class="badge bg-warning text-dark">Архив</span>',
    default                => (string)$page->status,
};
?>

<div class="d-flex gap-2 mb-3 flex-wrap">
    <?= Html::a('<i class="bi bi-pencil me-1"></i>Редактировать', ['update', 'id' => $page->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<i class="bi bi-eye me-1"></i>На сайте', $absoluteFrontendUrl, ['class' => 'btn btn-outline-secondary', 'target' => '_blank']) ?>

    <?php if (!$page->isPublished()): ?>
        <?= Html::a('<i class="bi bi-check-circle me-1"></i>Опубликовать', ['publish', 'id' => $page->id], [
            'class' => 'btn btn-success',
            'data-method' => 'post',
            'data-confirm' => 'Опубликовать страницу?',
        ]) ?>
    <?php endif; ?>

    <?php if (!$page->isDraft()): ?>
        <?= Html::a('<i class="bi bi-file-earmark me-1"></i>В черновик', ['draft', 'id' => $page->id], [
            'class' => 'btn btn-outline-secondary',
            'data-method' => 'post',
            'data-confirm' => 'Перевести в черновик?',
        ]) ?>
    <?php endif; ?>

    <!-- Добавить в меню -->
    <?= \Besnovatyj\Menu\widgets\add\AddItemWidget::widget([
        'endpoint' => Url::to('/Menu/backend/widget/create', true),
        'link'     => $frontendUrl,
        'name'     => $page->title,
    ]) ?>

    <?= Html::a('<i class="bi bi-trash me-1"></i>Удалить', ['delete', 'id' => $page->id], [
        'class'        => 'btn btn-outline-danger ms-auto',
        'data-method'  => 'post',
        'data-confirm' => 'Удалить страницу безвозвратно?',
    ]) ?>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">

        <!-- Контент страницы -->
        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="bi bi-body-text me-1"></i>Контент</div>
            <div class="card-body">
                <?= Yii::$app->formatter->asHtml($page->content, [
                    'Attr.AllowedRel'     => ['nofollow'],
                    'HTML.SafeObject'     => true,
                    'Output.FlashCompat'  => true,
                    'HTML.SafeIframe'     => true,
                    'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
                ]) ?>
            </div>
        </div>

    </div>

    <div class="col-12 col-lg-4">

        <!-- Основная информация -->
        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-1"></i>Информация</div>
            <div class="card-body p-0">
                <?= DetailView::widget([
                    'model'      => $page,
                    'options'    => ['class' => 'table table-sm mb-0'],
                    'attributes' => [
                        'id',
                        [
                            'label' => 'Статус',
                            'value' => $statusLabel,
                            'format' => 'raw',
                        ],
                        'title',
                        [
                            'label' => 'Slug',
                            'value' => '<code>' . Html::encode($page->slug) . '</code>',
                            'format' => 'raw',
                        ],
                        [
                            'label' => 'Раздел',
                            'value' => $page->group ? Html::encode($page->group->name) : '—',
                        ],
                        'sort_order',
                        [
                            'label' => 'Создана',
                            'value' => Yii::$app->formatter->asDatetime($page->created_at),
                        ],
                        [
                            'label' => 'Обновлена',
                            'value' => Yii::$app->formatter->asDatetime($page->updated_at),
                        ],
                    ],
                ]) ?>
            </div>
        </div>

        <!-- Краткое описание -->
        <?php if (!empty($page->excerpt)): ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="bi bi-card-text me-1"></i>Краткое описание</div>
            <div class="card-body text-muted">
                <?= Html::encode($page->excerpt) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- SEO -->
        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="bi bi-search me-1"></i>SEO</div>
            <div class="card-body p-0">
                <?= DetailView::widget([
                    'model'      => $page,
                    'options'    => ['class' => 'table table-sm mb-0'],
                    'attributes' => [
                        'meta.title:text:SEO-заголовок',
                        'meta.description:text:SEO-описание',
                        'meta.keywords:text:Ключевые слова',
                    ],
                ]) ?>
            </div>
        </div>

    </div>
</div>
