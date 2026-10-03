<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Contracts\adminMenu\AdminMenuLocation;
use Besnovatyj\Contracts\adminMenu\AdminMenuPlacement;

return [
    // Страницы
    [
        'label'     => 'Страницы',
        'iconClass' => 'bi bi-file-text me-1',
        'url'       => ['/Page/backend/page/index'],
        'active'    => static function () {
            return str_contains(\Yii::$app->request->url, 'Page/backend/page');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::LeftSidebar,
                    group: 'Page',
                    groupIcon: 'bi bi-journal-text',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],

    // Группы (разделы) страниц
    [
        'label'     => 'Разделы',
        'iconClass' => 'bi bi-diagram-3 me-1',
        'url'       => ['/Page/backend/group/index'],
        'active'    => static function () {
            return str_contains(\Yii::$app->request->url, 'Page/backend/group');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::LeftSidebar,
                    group: 'Page',
                    groupIcon: 'bi bi-journal-text',
                    groupPriority: 100,
                    priority: 200,
                ),
            ],
        ],
    ],
];
