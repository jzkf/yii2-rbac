<?php

use app\models\User;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var array $allRoles */
/** @var array $assignedRoles */
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'options' => [
        'autocomplete' => 'off',
    ]
]); ?>

<div class="card card-outline card-success mb-3">
    <div class="card-header">
        <i class="bi bi-user"></i> 基本信息
    </div>
    <div class="card-body">
        <?= $form->field($user, 'username')->textInput(['maxlength' => true, 'autofocus' => true]) ?>

        <?= $form->field($user, 'email')->textInput(['maxlength' => true, 'type' => 'email']) ?>

        <?= $form->field($user, 'mobile')->textInput(['maxlength' => 15]) ?>

        <?= $form->field($user, 'password')->passwordInput(['maxlength' => true])
            ->hint($user->isNewRecord ? '请输入密码（至少6位）' : '留空则不修改密码') ?>

        <?= $form->field($user, 'status')->dropDownList(
            \app\models\User::statuses(),
            ['prompt' => '请选择状态']
        ) ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <i class="bi bi-user-tag"></i> 角色分配
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="control-label">选择角色</label>
            <div class="mt-2" style="max-height: 300px; overflow-y: auto;">
                <?php if (empty($allRoles)): ?>
                    <p class="text-muted">暂无角色，请先创建角色</p>
                <?php else: ?>
                    <?php foreach ($allRoles as $role): ?>
                        <div class="form-check mb-2">
                            <?= Html::checkbox('roles[]', in_array($role->name, $assignedRoles), [
                                'value' => $role->name,
                                'id' => 'role-' . $role->name,
                                'class' => 'form-check-input'
                            ]) ?>
                            <label class="form-check-label" for="role-<?= $role->name ?>">
                                <?= Html::encode($role->description ?: $role->name) ?>
                                <small class="text-muted">(<?= Html::encode($role->name) ?>)</small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('<i class="bi bi-check"></i> 保存', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('取消', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
