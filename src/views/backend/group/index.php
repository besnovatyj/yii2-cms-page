<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\TreeManager\Manager\TreeDataSource;
use Besnovatyj\TreeManager\Manager\TreeWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View           $this
 * @var string         $title
 * @var TreeDataSource $treeDataSource
 */

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Страницы', 'url' => ['/Page/backend/page/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-group-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="h3 mb-0"><?= Html::encode($this->title) ?></div>
        <?= Html::a(
            '<i class="bi bi-file-text me-1"></i>К страницам',
            ['/Page/backend/page/index'],
            ['class' => 'btn btn-outline-secondary btn-sm'],
        ) ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?= TreeWidget::widget([
                'dataSource'       => $treeDataSource,
                'endpoints'        => [
                    'loadChildren'   => Url::to(['/Page/backend/group/load-children']),
                    'createNode'     => Url::to(['/Page/backend/group/create']),
                    'updateNode'     => Url::to(['/Page/backend/group/update']),
                    'deleteNode'     => Url::to(['/Page/backend/group/delete']),
                    'moveNode'       => Url::to(['/Page/backend/group/move']),
                    'toggleStatus'   => Url::to(['/Page/backend/group/toggle-status']),
                    'checkIntegrity' => Url::to(['/Page/backend/group/check-integrity']),
                ],
                'serverForms'      => [
                    'enabled'       => true,
                    'display'       => 'modal',
                    'errorStrategy' => 'both',
                    'operations'    => [
                        'create' => true,
                        'edit'   => true,
                    ],
                    'getFormUrl'    => Url::to(['/Page/backend/group/get-form']),
                ],
                'permissions'      => [
                    'canCreate' => true,
                    'canUpdate' => true,
                    'canDelete' => true,
                    'canMove'   => true,
                ],
                'titleField'        => 'title',
                'enablePersistence' => true,
                'storageKey'        => 'Page-group-tree-state',
                'containerOptions'  => [
                    'class' => 'page-group-tree-widget',
                ],
            ]) ?>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<CSS
.page-group-tree-widget {
    min-height: 400px;
}
CSS);
?>
