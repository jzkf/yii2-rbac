<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var \yii\rbac\Role $role */

$this->title = $role->name;
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$auth = Yii::$app->authManager;
$permissions = $auth->getPermissionsByRole($role->name);
?>
<div class="rbac-role-view">

    <div class="card card-outline card-info mb-3">
        <div class="card-header">
            <i class="bi bi-user-tag"></i> <?= Html::encode($this->title) ?>
            <div class="card-tools">
                <?= Html::a('权限设置', ['/rbac/role-permission/assign', 'name' => $role->name], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('编辑', ['update', 'name' => $role->name], ['class' => 'btn btn-success']) ?>
                <?= Html::a('删除', ['delete', 'name' => $role->name], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '确定要删除此角色吗？',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="card-body p-0">
            <?= DetailView::widget([
                'model' => $role,
                'options' => ['class' => 'table table-hover detail-view'],
                'attributes' => [
                    'name',
                    'description',
                    [
                        'label' => '类型',
                        'value' => $role->type === \yii\rbac\Item::TYPE_ROLE ? '角色' : '权限',
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-key"></i> 已分配的权限
        </div>
        <div class="card-body">
            <?php if (empty($permissions)): ?>
                <p class="text-muted">暂无权限</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($permissions as $permission): ?>
                        <li class="list-group-item">
                            <strong><?= Html::encode($permission->name) ?></strong>
                            <?php if ($permission->description): ?>
                                <br><small class="text-muted"><?= Html::encode($permission->description) ?></small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
