<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $roles */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-role-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-normal"><?= Html::encode($this->title) ?></h3>
        <?= Html::a('<i class="bi bi-plus"></i> 新增角色', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>角色名称</th>
                    <th>描述</th>
                    <th class="text-right">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($roles)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">暂无数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($roles as $role): ?>
                        <tr>
                            <td><?= Html::encode($role->name) ?></td>
                            <td><?= Html::encode($role->description) ?></td>
                            <td class="text-nowrap text-right">
                                <?= Html::a('查看', ['view', 'name' => $role->name], ['class' => 'btn btn-sm btn-info']) ?>
                                <?= Html::a('权限', ['/rbac/role-permission/assign', 'name' => $role->name], ['class' => 'btn btn-sm btn-primary']) ?>
                                <?= Html::a('编辑', ['update', 'name' => $role->name], ['class' => 'btn btn-sm btn-warning']) ?>
                                <?= Html::a('删除', ['delete', 'name' => $role->name], [
                                    'class' => 'btn btn-sm btn-danger',
                                    'data' => [
                                        'confirm' => '确定要删除此角色吗？',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
