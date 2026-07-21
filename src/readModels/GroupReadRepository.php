<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\readModels;

use Besnovatyj\Page\entities\Group;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

/**
 * Read-репозиторий для групп страниц (frontend)
 */
class GroupReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Group::class);
    }

    /**
     * Найти активную группу по slug
     */
    public function findBySlug(string $slug): ?Group
    {
        return Group::find()->active()->andWhere(['slug' => $slug])->one();
    }

    /**
     * Получить все активные корневые группы
     *
     * @return Group[]
     */
    public function getRoots(): array
    {
        return $this->treeScope->rootsQuery()
            ->andWhere(['status' => 1])
            ->all();
    }

    /**
     * Получить хлебные крошки для группы (от корня к текущему узлу)
     *
     * @return array [['label' => '...', 'url' => '...'], ...]
     */
    public function getBreadcrumbs(Group $group, callable $urlCallback): array
    {
        return $this->treeScope->breadcrumbs($group, 'name', $urlCallback);
    }

    /**
     * Получить список групп для выпадающего списка (dropdown)
     */
    public function dropdownList(?int $excludeId = null): array
    {
        return $this->treeScope->dropdownTree(excludeNodeId: $excludeId);
    }

    /**
     * Карта «slug => название» активных групп (для выбора цели URL-алиаса в админке).
     *
     * @return array<string,string>
     */
    public function slugNameMap(): array
    {
        return Group::find()->active()
            ->select(['name', 'slug'])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('slug')
            ->column();
    }
}
