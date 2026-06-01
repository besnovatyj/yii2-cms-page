<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\forms\backend;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Helpers\StringHelper;
use Besnovatyj\Meta\MetaForm;
use Besnovatyj\Page\entities\Group;
use Besnovatyj\Validators\SlugValidator;
use Besnovatyj\TreeManager\Manager\forms\TreeNodeFormInterface;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\Inflector;

/**
 * Форма создания/редактирования группы страниц (узла дерева)
 *
 * @property MetaForm $meta
 */
class GroupForm extends CompositeForm implements TreeNodeFormInterface
{
    // --- TreeNodeFormInterface ---
    public int|string|null $nodeId = null {
        get {
            return $this->nodeId;
        }
    }
    public int|string|null $parentId = null {
        get {
            return $this->parentId;
        }
    }
    public int|string $status = 0 {
        get {
            return $this->status;
        }
    }

    // --- Поля формы ---
    public string $name = '';
    public string $slug = '';
    public string $description = '';

    private ?Group $_group = null;

    public function __construct(?Group $group = null, ?int $parentId = null, $config = [])
    {
        $this->parentId = $parentId;
        if ($group !== null) {
            $this->nodeId = $group->id;
            $this->name = $group->name;
            $this->slug = $group->slug;
            $this->description = $group->description;
            $this->status = $group->status;
            $this->meta = new MetaForm($group->meta);
            $this->_group = $group;
        } else {
            $this->meta = new MetaForm();
        }
        parent::__construct($config);
    }

    public function beforeValidate(): bool
    {
        $this->status = (int)$this->status;
        $this->nodeId = (int)$this->nodeId;
        $this->parentId = (int)$this->parentId;

        $this->name = StringHelper::spaceReplace($this->name);
        $this->slug = $this->slug ? Inflector::slug($this->slug) : Inflector::slug($this->name);
        $this->description = StringHelper::spaceReplace($this->description);

        return parent::beforeValidate();
    }

    public function rules(): array
    {
        return [
            [['name', 'status'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['status', 'parentId', 'nodeId'], 'integer'],
            ['status', 'in', 'range' => [0, 1]],
            [['description'], 'string'],
            ['slug', SlugValidator::class],
            [
                ['name', 'slug'],
                'unique',
                'targetClass' => Group::class,
                'filter' => $this->_group ? ['<>', 'id', $this->_group->id] : null,
            ],
        ];
    }

    /**
     * Список групп для выпадающего списка (используется в форме выбора родителя)
     */
    public function parentGroupsList(): array
    {
        $scope = new TreeQueryScope(Group::class);
        return $scope->dropdownTree(excludeNodeId: $this->nodeId ? (int)$this->nodeId : null);
    }

    public function internalForms(): array
    {
        return ['meta'];
    }

    public function attributeLabels(): array
    {
        return [
            'name'        => 'Название группы',
            'slug'        => 'Slug (заполнится автоматически)',
            'description' => 'Описание группы',
            'parentId'    => 'Родительская группа',
            'status'      => 'Статус',
        ];
    }

    /**
     * Пустое formName() упрощает работу TreeController
     */
    public function formName(): string
    {
        return '';
    }
}
