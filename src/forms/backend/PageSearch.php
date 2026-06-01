<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\forms\backend;

use Besnovatyj\Page\entities\Page;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Форма поиска/фильтрации страниц в бэкэнде
 */
class PageSearch extends Model
{
    public string|int|null $id = null;
    public string|null $title = null;
    public string|null $slug = null;
    public string|int|null $group_id = null;
    public string|int|null $status = null;

    public function rules(): array
    {
        return [
            [['id', 'group_id', 'status'], 'integer'],
            [['title', 'slug'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'group_id' => 'Группа',
            'status'   => 'Статус',
            'title'    => 'Заголовок',
            'slug'     => 'Slug',
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Page::find()->with(['group']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC, 'id' => SORT_DESC],
                'attributes' => ['id', 'title', 'slug', 'status', 'sort_order', 'created_at'],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['group_id' => $this->group_id]);
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }

    /**
     * Список статусов для фильтра
     */
    public function statusList(): array
    {
        return [
            Page::STATUS_DRAFT     => 'Черновик',
            Page::STATUS_PUBLISHED => 'Опубликована',
            Page::STATUS_ARCHIVED  => 'Архив',
        ];
    }
}
