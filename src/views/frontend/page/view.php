<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\Shortcode\widgets\ShortcodeContent;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\base\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var $this View */
/** @var $page Page */

$this->title = $page->title;

$this->params['og:title'] = $this->title;

if ($page->group !== null) {
    $this->params['breadcrumbs'] = new TreeQueryScope(Group::class)->breadcrumbs($page->group, urlCallback: function ($item) use ($page) {
        if ($item->id !== $page->group->id) {
            return Url::to(['group', 'slug' => $item->slug]);
        }
        return false;
    });
}

$this->registerMetaTag(['name' => 'keywords', 'content' => $page->meta->keywords]);
$this->registerMetaTag(['name' => 'description', 'content' => $page->meta->description]);

if (Yii::$app->getModule('Config') instanceof Module) {
    $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->getModule('Config')->params['frontend']['app']['name']]);
}
?>

<article class="page-content">
    <header class="page-header mb-4">
        <h1><?= Html::encode($page->title) ?></h1>
        <?php if ($page->group !== null): ?>
            <div class="page-group text-muted small">
                <i class="bi bi-folder me-1"></i>
                <a href="<?= Url::to(['/Page/page/group', 'slug' => $page->group->slug]) ?>">
                    <?= Html::encode($page->group->name) ?>
                </a>
            </div>
        <?php endif; ?>
    </header>

    <div class="page-body">
        <?= ShortcodeContent::widget(['content' => $page->content]) ?>
    </div>
</article>
