<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\readModels;

use Besnovatyj\Contracts\search\SearchDocument;
use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

/**
 * Чтение страниц ДЛЯ ФРОНТЕНДА.
 *
 * Каждый метод отдаёт только доступное анонимному посетителю: страница опубликована и лежит в
 * видимом разделе (см. {@see \Besnovatyj\Page\entities\queries\PageQuery::visible()}).
 * Выборки для админки, которой положено видеть скрытое, — в
 * {@see \Besnovatyj\Page\repositories\PageRepository}.
 */
class PageReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Group::class);
    }

    /**
     * Найти доступную посетителю страницу по slug (опубликована и раздел не скрыт)
     */
    public function findBySlug(string $slug): ?Page
    {
        return Page::find()->visible()->andWhere(['slug' => $slug])->one();
    }

    /**
     * Найти доступную посетителю страницу по ID
     */
    public function find(int $id): ?Page
    {
        return Page::find()->visible()->andWhere(['id' => $id])->one();
    }

    /**
     * Получить все опубликованные страницы группы (включая дочерние группы)
     */
    public function getAllByGroup(Group $group): DataProviderInterface
    {
        $groupIds = $this->treeScope->descendantIds($group, andSelf: true);
        $query = Page::find()->visible()
            ->andWhere(['group_id' => $groupIds])
            ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]);

        return $this->makeProvider($query);
    }

    /**
     * Получить все опубликованные страницы без группы
     */
    public function getAllWithoutGroup(): DataProviderInterface
    {
        $query = Page::find()->visible()->withoutGroup()
            ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]);

        return $this->makeProvider($query);
    }

    /**
     * Получить все опубликованные страницы (для sitemap, виджетов)
     *
     * @return Page[]
     */
    public function getAllPublished(int $limit = 100): array
    {
        return Page::find()->visible()
            ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Карта «slug => заголовок» опубликованных страниц (для выбора цели URL-алиаса в админке).
     *
     * @return array<string,string>
     */
    public function slugTitleMap(): array
    {
        return Page::find()->visible()
            ->select(['title', 'slug'])
            ->orderBy(['title' => SORT_ASC])
            ->indexBy('slug')
            ->column();
    }

    /**
     * Опубликованные страницы для сквозного поиска.
     *
     * Отдаёт ровно то, что доступно анонимному посетителю: черновики и архив в публичный индекс
     * попасть не должны. Читает пачками и отдаёт генератором — полная переиндексация не должна
     * держать в памяти весь контент сайта.
     *
     * @return iterable<SearchDocument>
     */
    public function searchDocuments(): iterable
    {
        $query = Page::find()->visible()->orderBy(['id' => SORT_ASC]);

        /** @var Page $page */
        foreach ($query->each(100) as $page) {
            yield new SearchDocument(
                type: 'page.page',
                entityId: (int)$page->id,
                route: '/Page/page/view',
                params: ['slug' => $page->slug],
                title: (string)$page->title,
                text: (string)$page->content,
                keywords: (string)($page->meta->keywords ?? ''),
                excerpt: $page->excerpt,
                // created_at здесь DATETIME-строка, а контракт ждёт Unix-timestamp.
                date: $page->created_at === null ? null : (strtotime($page->created_at) ?: null),
            );
        }
    }

    private function makeProvider(\yii\db\ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);
    }
}
