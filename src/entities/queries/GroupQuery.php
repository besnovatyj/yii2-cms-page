<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\entities\queries;

use yii\db\ActiveQuery;

/**
 * GroupQuery — кастомный запрос для сущности Group
 */
class GroupQuery extends ActiveQuery
{
    /**
     * Только активные группы
     */
    public function active(): static
    {
        return $this->andWhere(['status' => 1]);
    }
}
