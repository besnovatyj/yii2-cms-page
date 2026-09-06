<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\entities\queries;

use Besnovatyj\Page\entities\Group;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

/**
 * GroupQuery — кастомный запрос для сущности Group
 */
class GroupQuery extends ActiveQuery
{
    /**
     * Раздел опубликован сам по себе (без учёта дерева).
     *
     * Для фронтенда этого мало: раздел внутри скрытого родителя тоже не должен быть доступен —
     * см. {@see visible()}.
     */
    public function active(?string $alias = null): static
    {
        return $this->andWhere([($alias ?? Group::tableName()) . '.status' => Group::STATUS_ACTIVE]);
    }

    /**
     * Раздел доступен анонимному посетителю: опубликован сам и не спрятан ни одним из предков.
     *
     * Скрытие родителя обязано скрывать всю ветку — иначе дочерний раздел остаётся открыт по
     * прямой ссылке, хотя из навигации он исчез. Проверка идёт по ключам Nested Sets одним
     * подзапросом; виртуальный корень (`depth = 0`) исключён — он служебный и статуса не имеет.
     */
    public function visible(?string $alias = null): static
    {
        $table = Group::tableName();
        $self = $alias ?? $table;

        $hiddenAncestor = (new Query())
            ->select(new Expression('1'))
            ->from(['anc' => $table])
            ->where(new Expression(
                "anc.[[tree]] = {$self}.[[tree]] AND anc.[[lft]] < {$self}.[[lft]] AND anc.[[rgt]] > {$self}.[[rgt]]",
            ))
            ->andWhere(['>', 'anc.depth', 0])
            ->andWhere(['<>', 'anc.status', Group::STATUS_ACTIVE]);

        return $this->active($alias)->andWhere(['not exists', $hiddenAncestor]);
    }
}
