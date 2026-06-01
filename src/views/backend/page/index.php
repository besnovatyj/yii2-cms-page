<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Backend\Widgets\grid\ActionColumn;
use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\Page\forms\backend\PageSearch;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\bootstrap5\Html;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel PageSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Статические страницы';
$this->params['breadcrumbs'][] = $this->title;

$groupScope = new TreeQueryScope(Group::class);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <?= Html::a(
            '<i class="bi bi-plus-lg me-1"></i>Создать страницу',
            ['create'],
            ['class' => 'btn btn-success'],
        ) ?>
    </div>
    <div>
        <?= Html::a(
            '<i class="bi bi-diagram-3 me-1"></i>Разделы страниц',
            ['/Page/backend/group/index'],
            ['class' => 'btn btn-outline-secondary'],
        ) ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => "{summary}\n{items}",
            'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'title',
                    'label'     => 'Заголовок',
                    'value'     => static function (Page $model): string {
                        $title = Html::a(Html::encode($model->title), ['update', 'id' => $model->id]);
                        if (!empty($model->excerpt)) {
                            $title .= '<br><small class="text-muted">' . Html::encode(mb_substr($model->excerpt, 0, 80)) . '…</small>';
                        }
                        return $title;
                    },
                    'format'    => 'raw',
                ],

                [
                    'attribute' => 'group_id',
                    'label'     => 'Раздел',
                    'filter'    => array_merge(['' => 'Все'], $groupScope->dropdownTree()),
                    'value'     => static fn (Page $model): string =>
                        $model->group ? Html::encode($model->group->name) : '<span class="text-muted">—</span>',
                    'format'    => 'raw',
                ],

                [
                    'attribute' => 'slug',
                    'label'     => 'Slug',
                    'value'     => static fn (Page $model): string => '<code class="small">' . Html::encode($model->slug) . '</code>',
                    'format'    => 'raw',
                ],

                [
                    'attribute' => 'status',
                    'label'     => 'Статус',
                    'filter'    => array_merge(['' => 'Все'], $searchModel->statusList()),
                    'value'     => static function (Page $model): string {
                        return match ($model->status) {
                            Page::STATUS_PUBLISHED => '<span class="badge bg-success">Опубликована</span>',
                            Page::STATUS_DRAFT     => '<span class="badge bg-secondary">Черновик</span>',
                            Page::STATUS_ARCHIVED  => '<span class="badge bg-warning text-dark">Архив</span>',
                            default                => (string)$model->status,
                        };
                    },
                    'format'    => 'raw',
                ],

                [
                    'attribute' => 'sort_order',
                    'label'     => 'Сорт.',
                    'headerOptions' => ['style' => 'width:60px'],
                ],

                [
                    'attribute'     => 'created_at',
                    'label'         => 'Создана',
                    'format'        => 'datetime',
                    'filter'        => false,
                    'headerOptions' => ['style' => 'width:130px'],
                ],

                [
                    'class'    => ActionColumn::class,
                    'header'   => '',
                    'template' => '{update} {view} {publish} {draft} {archive} {delete}',
                    'headerOptions' => ['style' => 'width:160px'],
                    'buttons'  => [
                        'update' => static fn ($url, Page $model): string => Html::a(
                            '<i class="bi bi-pencil"></i>',
                            ['update', 'id' => $model->id],
                            ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'Редактировать'],
                        ),
                        'view' => static fn ($url, Page $model): string => Html::a(
                            '<i class="bi bi-eye"></i>',
                            Url::to(['view', 'id' => $model->id], true),
                            ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Просмотр'],
                        ),
                        'publish' => static function ($url, Page $model): string {
                            if ($model->isPublished()) return '';
                            return Html::a(
                                '<i class="bi bi-check-circle"></i>',
                                ['publish', 'id' => $model->id],
                                [
                                    'class'        => 'btn btn-sm btn-outline-success',
                                    'title'        => 'Опубликовать',
                                    'data-method'  => 'post',
                                    'data-confirm' => 'Опубликовать страницу?',
                                ],
                            );
                        },
                        'draft' => static function ($url, Page $model): string {
                            if ($model->isDraft()) return '';
                            return Html::a(
                                '<i class="bi bi-file-earmark"></i>',
                                ['draft', 'id' => $model->id],
                                [
                                    'class'        => 'btn btn-sm btn-outline-secondary',
                                    'title'        => 'В черновик',
                                    'data-method'  => 'post',
                                    'data-confirm' => 'Перевести в черновик?',
                                ],
                            );
                        },
                        'archive' => static function ($url, Page $model): string {
                            if ($model->isArchived()) return '';
                            return Html::a(
                                '<i class="bi bi-archive"></i>',
                                ['archive', 'id' => $model->id],
                                [
                                    'class'        => 'btn btn-sm btn-outline-warning',
                                    'title'        => 'Архивировать',
                                    'data-method'  => 'post',
                                    'data-confirm' => 'Архивировать страницу?',
                                ],
                            );
                        },
                        'delete' => static fn ($url, Page $model): string => Html::a(
                            '<i class="bi bi-trash"></i>',
                            ['delete', 'id' => $model->id],
                            [
                                'class'        => 'btn btn-sm btn-outline-danger',
                                'title'        => 'Удалить',
                                'data-method'  => 'post',
                                'data-confirm' => 'Удалить страницу безвозвратно?',
                            ],
                        ),
                    ],
                ],
            ],
        ]) ?>
    </div>
    <div class="card-footer clearfix">
        <?= LinkPager::widget(['pagination' => $dataProvider->getPagination()]) ?>
    </div>
</div>
