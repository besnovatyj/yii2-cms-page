<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Page\forms\backend\GroupForm;
use yii\bootstrap5\ActiveForm;

/**
 * @var GroupForm $model
 */
?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model) ?>

<?php if ($model->parentId !== null): ?>
    <?= $form->field($model, 'parentId')->hiddenInput()->label(false) ?>
<?php endif; ?>
<?= $form->field($model, 'nodeId')->hiddenInput()->label(false) ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Название раздела']) ?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'placeholder' => 'slug-avtomaticheski']) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Описание раздела (необязательно)']) ?>
<?= $form->field($model, 'status')->dropDownList([1 => 'Активен', 0 => 'Неактивен'], ['class' => 'form-select']) ?>

<div class="card border-0">
    <div class="card-header d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
        <span class="text-muted small">SEO</span>
        <a class="btn btn-link btn-sm collapse-button text-decoration-none p-0" data-bs-toggle="collapse" href="#group-seo-fields"
           role="button" aria-expanded="false">
            <i class="bi bi-plus-lg"></i>
            <i class="bi bi-dash-lg"></i>
        </a>
    </div>
    <div class="collapse" id="group-seo-fields">
        <div class="pt-2">
            <?= $form->field($model->meta, 'title')->textInput(['placeholder' => 'SEO-заголовок']) ?>
            <?= $form->field($model->meta, 'description')->textarea(['rows' => 2, 'placeholder' => 'SEO-описание']) ?>
            <?= $form->field($model->meta, 'keywords')->textInput(['placeholder' => 'Ключевые слова']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
