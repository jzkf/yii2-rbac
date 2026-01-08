<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \yii\rbac\Permission $permission */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="rbac-permission-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'autocomplete' => 'off',
        ]
    ]); ?>

    <div class="card card-outline card-success mb-3">
        <div class="card-header">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="control-label">权限名称</label>
                <?= Html::textInput('name', $permission->name, [
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => '例如: /backend/post/index'
                ]) ?>
                <div class="help-block">权限名称，通常为路由路径，例如: /backend/post/index</div>
            </div>

            <div class="form-group">
                <label class="control-label">描述</label>
                <?= Html::textInput('description', $permission->description, [
                    'class' => 'form-control',
                    'placeholder' => '权限描述'
                ]) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="bi bi-check"></i> 保存', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
