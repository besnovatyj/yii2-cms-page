<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\readModels;

use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

/**
 * Read-репозиторий для получения страниц (frontend)
 */
class PageReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Group::class);
    }

    /**
     * Найти опубликованную страницу по slug
     */
    public function findBySlug(string $slug): ?Page
    {
        return Page::find()->published()->andWhere(['slug' => $slug])->one();
    }

    /**
     * Найти страницу по ID (только опубликованные)
     */
    public function find(int $id): ?Page
    {
        return Page::find()->published()->andWhere(['id' => $id])->one();
    }

    /**
     * Получить все опубликованные страницы группы (включая дочерние группы)
     */
    public function getAllByGroup(Group $group): DataProviderInterface
    {
        $groupIds = $this->treeScope->descendantIds($group, andSelf: true);
        $query = Page::find()->published()
            ->andWhere(['group_id' => $groupIds])
            ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]);

        return $this->makeProvider($query);
    }

    /**
     * Получить все опубликованные страницы без группы
     */
    public function getAllWithoutGroup(): DataProviderInterface
    {
        $query = Page::find()->published()->withoutGroup()
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
        return Page::find()->published()
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
        return Page::find()->published()
            ->select(['title', 'slug'])
            ->orderBy(['title' => SORT_ASC])
            ->indexBy('slug')
            ->column();
    }

    private function makeProvider(\yii\db\ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);
    }
}
