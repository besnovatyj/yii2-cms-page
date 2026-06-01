<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Meta\Meta;
use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\forms\backend\GroupForm;
use Besnovatyj\TreeManager\Manager\entities\Node;
use Besnovatyj\TreeManager\Manager\forms\TreeNodeFormInterface;
use Besnovatyj\TreeManager\Manager\TreeManager;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

/**
 * Конфигурация DI-контейнера для модуля Page
 */
return function (\yii\di\Container $container): void {
    // TreeManager для групп страниц
    $container->setSingleton('page.tree.manager', function () use ($container): TreeManager {
        return new TreeManager(
            modelClass: Group::class,
            entityFactory: function (TreeNodeFormInterface $form): Group {
                return Group::create(
                    $form->name,
                    $form->slug,
                    $form->description,
                    new Meta(
                        $form->meta->title,
                        $form->meta->description,
                        $form->meta->keywords,
                    ),
                );
            },
            entityUpdater: function (Node $node, TreeNodeFormInterface $form): Node {
                /** @var Group $node */
                $node->edit(
                    $form->name,
                    $form->slug,
                    $form->description,
                    new Meta(
                        $form->meta->title,
                        $form->meta->description,
                        $form->meta->keywords,
                    ),
                );
                return $node;
            },
        );
    });

    // TreeQueryScope для чтения дерева групп
    $container->setSingleton('page.tree.scope', function () use ($container): TreeQueryScope {
        return new TreeQueryScope(Group::class);
    });
};
