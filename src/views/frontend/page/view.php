<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\entities\Page;
use yii\web\View;

/* @var $this View */
/* @var $page Page */
/* @var $breadcrumbs array */

$this->title = $page->getSeoTitle();

if (!empty($page->meta->description)) {
    $this->registerMetaTag(['name' => 'description', 'content' => $page->meta->description]);
}
if (!empty($page->meta->keywords)) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $page->meta->keywords]);
}

foreach ($breadcrumbs as $crumb) {
    $this->params['breadcrumbs'][] = $crumb;
}
?>

<article class="page-content">
    <header class="page-header mb-4">
        <h1><?= \yii\helpers\Html::encode($page->title) ?></h1>

        <?php if ($page->group !== null): ?>
            <div class="page-group text-muted small">
                <i class="bi bi-folder me-1"></i>
                <a href="<?= \yii\helpers\Url::to(['/Page/page/group', 'slug' => $page->group->slug]) ?>">
                    <?= \yii\helpers\Html::encode($page->group->name) ?>
                </a>
            </div>
        <?php endif; ?>
    </header>

    <div class="page-body">
        <?= \Besnovatyj\Shortcode\widgets\ShortcodeContent::widget(['content' => $page->content]) ?>
    </div>
</article>
