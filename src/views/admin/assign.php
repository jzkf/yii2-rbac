<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \app\models\User $user */
/** @var array $allRoles */
/** @var array $assignedRoles */

$this->title = '角色分配: ' . $user->username;
$this->params['breadcrumbs'][] = ['label' => '管理员角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-admin-assign">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-key"></i> <?= Html::encode($this->title) ?>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="form-group">
                <label class="control-label">选择角色</label>
                <div class="mt-2">
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

            <div class="form-group mt-3">
                <?= Html::submitButton('<i class="bi bi-check"></i> 保存', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('取消', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
