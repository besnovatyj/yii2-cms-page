<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\entities\Page;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;

/* @var $this View */
/* @var $group Group */
/* @var $dataProvider ActiveDataProvider */
/* @var $breadcrumbs array */

$this->title = $group->getSeoTitle();

if (!empty($group->meta->description)) {
    $this->registerMetaTag(['name' => 'description', 'content' => $group->meta->description]);
}

foreach ($breadcrumbs as $crumb) {
    $this->params['breadcrumbs'][] = $crumb;
}
?>

<section class="container mt-3 mb-5">
    <header class="mb-4">
        <h1><?= Html::encode($group->name) ?></h1>
        <?php if (!empty($group->description)): ?>
            <p class="lead text-muted"><?= Html::encode($group->description) ?></p>
        <?php endif; ?>
    </header>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'    => '<p class="text-muted">В этом разделе пока нет страниц.</p>',
        'itemView'     => static function (Page $page): string {
            $url   = Url::to(['/Page/page/view', 'slug' => $page->slug]);
            $title = Html::a(Html::encode($page->title), $url, ['class' => 'fw-medium text-decoration-none']);

            $excerpt = '';
            if (!empty($page->excerpt)) {
                $excerpt = '<p class="text-muted mb-0 mt-1 small">' . Html::encode($page->excerpt) . '</p>';
            }

            return '<div class="list-group-item list-group-item-action py-3">'
                . '<div class="d-flex align-items-center gap-2">'
                . '<i class="bi bi-file-text text-muted"></i>'
                . '<div>'
                . '<div class="fw-medium">' . $title . '</div>'
                . $excerpt
                . '</div>'
                . '</div>'
                . '</div>';
        },
        'layout'       => '<div class="list-group list-group-flush">{items}</div><div class="mt-4 text-center">{pager}</div>',
    ]) ?>
</section>
