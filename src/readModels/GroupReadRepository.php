<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\readModels;

use Besnovatyj\Contracts\search\SearchDocument;
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

    /**
     * Активные разделы страниц для сквозного поиска.
     *
     * Раздел — такая же посадочная страница, как и обычная: у него есть название, описание и
     * собственный адрес, поэтому в выдаче он полезен. Вес источника ниже, чем у страниц: раздел
     * почти всегда лишь путь к нужному материалу.
     *
     * @return iterable<SearchDocument>
     */
    public function searchDocuments(): iterable
    {
        $query = Group::find()->active()->orderBy(['id' => SORT_ASC]);

        /** @var Group $group */
        foreach ($query->each(100) as $group) {
            yield new SearchDocument(
                type: 'page.group',
                entityId: (int)$group->id,
                route: '/Page/page/group',
                params: ['slug' => $group->slug],
                title: (string)$group->name,
                text: (string)$group->description,
                keywords: (string)($group->meta->keywords ?? ''),
            );
        }
    }
}
