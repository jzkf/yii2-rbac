<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \yii\rbac\Role $role */
/** @var array $allPermissions */
/** @var array $assignedPermissions */

$this->title = '权限设置: ' . $role->name . '(' . $role->description . ')';
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['/rbac/role/index']];
$this->params['breadcrumbs'][] = ['label' => $role->name, 'url' => ['/rbac/role/view', 'name' => $role->name]];
$this->params['breadcrumbs'][] = '权限设置';

?>
<div class="rbac-role-permission-assign">
    <?php $form = ActiveForm::begin(); ?>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-key"></i> <?= Html::encode($this->title) ?>
                </div>
                <?php if (!empty($allPermissions)): ?>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn" id="select-all-permissions">
                            <i class="bi bi-check-square"></i> 全选
                        </button>
                        <button type="button" class="btn" id="deselect-all-permissions">
                            <i class="bi bi-square"></i> 取消全选
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">

            <?php if (empty($allPermissions)): ?>
                <p class="text-muted">暂无权限，请先创建权限</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($allPermissions as $permission): ?>
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <?= Html::checkbox('permissions[]', in_array($permission->name, $assignedPermissions), [
                                    'value' => $permission->name,
                                    'id' => 'permission-' . $permission->name,
                                    'class' => 'form-check-input permission-checkbox'
                                ]) ?>
                                <label class="form-check-label" for="permission-<?= $permission->name ?>">
                                    <strong><?= Html::encode($permission->name) ?></strong>
                                    <?php if ($permission->description): ?>
                                        <br><small class="text-muted"><?= Html::encode($permission->description) ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <div class="card-footer">
            <?= Html::submitButton('<i class="bi bi-check"></i> 保存', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('取消', ['/rbac/role/view', 'name' => $role->name], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
// 全选/取消全选功能
$this->registerJs("
    (function() {
        var selectAllBtn = document.getElementById('select-all-permissions');
        var deselectAllBtn = document.getElementById('deselect-all-permissions');
        var checkboxes = document.querySelectorAll('.permission-checkbox');
        
        if (selectAllBtn && deselectAllBtn && checkboxes.length > 0) {
            // 全选
            selectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = true;
                });
            });
            
            // 取消全选
            deselectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                });
            });
        }
    })();
");
?>