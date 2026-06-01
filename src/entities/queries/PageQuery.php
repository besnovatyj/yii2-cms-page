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
