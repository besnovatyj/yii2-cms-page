<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\forms\backend;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Helpers\StringHelper;
use Besnovatyj\Meta\MetaForm;
use Besnovatyj\Page\entities\Page;
use Besnovatyj\Validators\SlugValidator;
use yii\helpers\Inflector;

/**
 * Форма создания/редактирования статической страницы
 *
 * @property MetaForm $meta
 */
class PageForm extends CompositeForm
{
    public int|null $group_id = null;
    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';
    public int $status = Page::STATUS_DRAFT;
    public int $sort_order = 0;

    private ?Page $_page = null;

    public function beforeValidate(): bool
    {
        $this->title = StringHelper::spaceReplace($this->title);
        $this->slug = $this->slug ? Inflector::slug($this->slug) : Inflector::slug($this->title);
        return parent::beforeValidate();
    }

    public function __construct(?Page $page = null, $config = [])
    {
        if ($page !== null) {
            $this->_page = $page;
            $this->group_id = $page->group_id;
            $this->title = $page->title;
            $this->slug = $page->slug;
            $this->excerpt = (string)$page->excerpt;
            $this->content = (string)$page->content;
            $this->status = $page->status;
            $this->sort_order = $page->sort_order;
            $this->meta = new MetaForm($page->meta);
        } else {
            $this->meta = new MetaForm();
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['title', 'status'], 'required'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['group_id', 'status', 'sort_order'], 'integer'],
            ['group_id', 'default', 'value' => null],
            ['status', 'in', 'range' => [Page::STATUS_DRAFT, Page::STATUS_PUBLISHED, Page::STATUS_ARCHIVED]],
            ['sort_order', 'default', 'value' => 0],
            [['excerpt', 'content'], 'string'],
            ['slug', SlugValidator::class],
            [
                'slug',
                'unique',
                'targetClass' => Page::class,
                'filter' => $this->_page ? ['<>', 'id', $this->_page->id] : null,
                'message' => 'Такой slug уже занят другой страницей.',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'group_id'   => 'Группа (раздел)',
            'title'      => 'Заголовок страницы',
            'slug'       => 'Slug (заполнится автоматически)',
            'excerpt'    => 'Краткое описание (для превью)',
            'content'    => 'Контент',
            'status'     => 'Статус',
            'sort_order' => 'Порядок сортировки',
        ];
    }

    protected function internalForms(): array
    {
        return ['meta'];
    }
}
