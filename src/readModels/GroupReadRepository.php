<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\readModels;

use Besnovatyj\Contracts\search\SearchDocument;
use Besnovatyj\Contracts\sitemap\SitemapUrl;
use Besnovatyj\Page\entities\Group;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

/**
 * Чтение разделов страниц ДЛЯ ФРОНТЕНДА.
 *
 * Отдаёт только видимые разделы: опубликованные и не спрятанные ни одним из предков
 * (см. {@see \Besnovatyj\Page\entities\queries\GroupQuery::visible()}). Списки для админки —
 * в {@see \Besnovatyj\Page\repositories\GroupRepository}.
 */
class GroupReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Group::class);
    }

    /**
     * Найти доступный посетителю раздел по slug (сам активен и предки не скрыты)
     */
    public function findBySlug(string $slug): ?Group
    {
        return Group::find()->visible()->andWhere(['slug' => $slug])->one();
    }

    /**
     * Получить все активные корневые группы
     *
     * @return Group[]
     */
    public function getRoots(): array
    {
        return $this->treeScope->rootsQuery()->visible()->all();
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
        return Group::find()->visible()
            ->select(['name', 'slug'])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('slug')
            ->column();
    }

    /**
     * Активные разделы страниц для карты сайта.
     *
     * Обход в порядке дерева (`tree`, `lft`) и глубина узла отдаются как есть: человеческая карта
     * рисует по ним отступ, а строить вложенные списки провайдеру не приходится — это забота
     * представления.
     *
     * Отпечатка свежести у разделов нет: колонок времени в дереве нет, а придумывать признак,
     * который не заметит переименования, значило бы получить молча устаревшую карту. Разделов
     * немного, полный обход дёшев.
     *
     * @return iterable<SitemapUrl>
     */
    public function sitemapUrls(): iterable
    {
        $query = Group::find()->visible()->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]);

        /** @var Group $group */
        foreach ($query->each(200) as $group) {
            yield new SitemapUrl(
                route: '/Page/page/group',
                params: ['slug' => $group->slug],
                title: (string)$group->name,
                depth: (int)$group->depth,
            );
        }
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
        $query = Group::find()->visible()->orderBy(['id' => SORT_ASC]);

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
