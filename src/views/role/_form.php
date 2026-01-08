<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \yii\rbac\Role $role */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="rbac-role-form">

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
                <label class="control-label">角色名称</label>
                <?= Html::textInput('name', $role->name, [
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => '例如: admin'
                ]) ?>
                <div class="help-block">角色名称，例如: admin, manager</div>
            </div>

            <div class="form-group">
                <label class="control-label">描述</label>
                <?= Html::textInput('description', $role->description, [
                    'class' => 'form-control',
                    'placeholder' => '角色描述'
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
