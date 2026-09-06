<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\entities\queries;

use yii\db\ActiveQuery;

/**
 * PageQuery — кастомный запрос для сущности Page
 */
class PageQuery extends ActiveQuery
{
    /**
     * Только опубликованные страницы
     */
    public function published(): static
    {
        return $this->andWhere(['status' => \Besnovatyj\Page\entities\Page::STATUS_PUBLISHED]);
    }

    /**
     * Страница доступна анонимному посетителю: опубликована сама И лежит в видимом разделе.
     *
     * Одной публикации мало: скрытый раздел не должен «протекать» на фронт своими страницами ни
     * через списки, ни через прямую ссылку. Раздел проверяется целиком, вместе с предками
     * (см. {@see GroupQuery::visible()}).
     *
     * Страница без раздела (`group_id` NULL) видна: скрывать её не за что.
     */
    public function visible(?string $alias = null): static
    {
        $column = ($alias ? $alias . '.' : '') . 'group_id';

        return $this->published()->andWhere([
            'or',
            [$column => null],
            [$column => \Besnovatyj\Page\entities\Group::find()->visible()->select('id')],
        ]);
    }

    /**
     * Только черновики
     */
    public function draft(): static
    {
        return $this->andWhere(['status' => \Besnovatyj\Page\entities\Page::STATUS_DRAFT]);
    }

    /**
     * Страницы заданной группы
     */
    public function byGroup(int $groupId): static
    {
        return $this->andWhere(['group_id' => $groupId]);
    }

    /**
     * Страницы без группы
     */
    public function withoutGroup(): static
    {
        return $this->andWhere(['group_id' => null]);
    }
}
