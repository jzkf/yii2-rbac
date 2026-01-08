<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $permissions */

$this->title = '权限管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-permission-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-normal"><?= Html::encode($this->title) ?></h3>
        <div>
            <?= Html::a('<i class="bi bi-sync"></i> 扫描权限', ['scan'], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => '确定要扫描控制器并自动生成新权限吗？',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="bi bi-plus"></i> 新增权限', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>权限名称</th>
                    <th>描述</th>
                    <th class="text-right">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($permissions)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">暂无数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($permissions as $permission): ?>
                        <tr>
                            <td><?= Html::encode($permission->name) ?></td>
                            <td><?= Html::encode($permission->description) ?></td>
                            <td class="text-nowrap text-right">
                                <?= Html::a('查看', ['view', 'name' => $permission->name], ['class' => 'btn btn-sm btn-info']) ?>
                                <?= Html::a('编辑', ['update', 'name' => $permission->name], ['class' => 'btn btn-sm btn-warning']) ?>
                                <?= Html::a('删除', ['delete', 'name' => $permission->name], [
                                    'class' => 'btn btn-sm btn-danger',
                                    'data' => [
                                        'confirm' => '确定要删除此权限吗？',
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
