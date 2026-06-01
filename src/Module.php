<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Page;

use common\components\module\BaseModule;

/**
 * Модуль управления статическими страницами
 */
class Module extends BaseModule
{
    public const bool EDITABLE = true;
    public const string VERSION = '2.0.0';

    public static function getAdminMenu(): array
    {
        return require __DIR__ . '/config/adminMenu.php';
    }

    public static function getConfig(): array
    {
        return require __DIR__ . '/config/config.php';
    }

    public static function getOptions(): array
    {
        return require __DIR__ . '/config/options.php';
    }

    public static function getDependencies(): array
    {
        return require __DIR__ . '/config/dependencies.php';
    }

    public static function setContainerConfig(): void
    {
        (require __DIR__ . '/config/container.php')(\Yii::$container);
    }
}
