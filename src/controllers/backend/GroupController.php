<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page\controllers\backend;

use Besnovatyj\Page\entities\Group;
use Besnovatyj\Page\forms\backend\GroupForm;
use Besnovatyj\TreeManager\Manager\controllers\TreeController;
use Besnovatyj\TreeManager\Manager\TreeDataSource;
use Yii;

/**
 * Контроллер управления группами (разделами) статических страниц
 *
 * Наследует TreeController для полного CRUD дерева групп.
 */
class GroupController extends TreeController
{
    public function __construct($id, $module, $config = [])
    {
        $this->treeManager = Yii::$container->get('page.tree.manager');

        $this->dataSource = new TreeDataSource(
            Group::class,
            static function (Group $model): array {
                return [
                    'id'    => $model->id,
                    'title' => $model->name,
                    'slug'  => $model->slug,
                ];
            },
            'sort_order',
            'sort_order',
        );

        $this->createFormClass = GroupForm::class;
        $this->updateFormClass = GroupForm::class;
        $this->formView        = '_form';
        $this->indexTitle      = 'Группы (разделы) страниц';

        parent::__construct($id, $module, $config);
    }
}
