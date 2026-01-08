<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $stats */

$this->title = 'RBAC 管理面板';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="rbac-default-index">
    <h3 class="fw-normal"><?= Html::encode($this->title) ?></h3>

    <!-- 统计面板 -->
    <div class="row mb-3">
        <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">管理员数量</h5>
                            <h2 class="mb-0"><?= Html::encode($stats['adminCount']) ?></h2>
                        </div>
                        <div class="text-primary" style="font-size: 3rem;">
                            <i class="bi bi-users"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <?= Html::a('<i class="bi bi-list"></i> 查看列表', ['/rbac/admin/index'], ['class' => 'btn btn-sm btn-primary']) ?>
                        <?= Html::a('<i class="bi bi-plus"></i> 新增管理员', ['/rbac/admin/create'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
            <div class="card card-outline card-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">角色数量</h5>
                            <h2 class="mb-0"><?= Html::encode($stats['roleCount']) ?></h2>
                        </div>
                        <div class="text-success" style="font-size: 3rem;">
                            <i class="bi bi-user-tag"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <?= Html::a('<i class="bi bi-list"></i> 查看列表', ['/rbac/role/index'], ['class' => 'btn btn-sm btn-success']) ?>
                        <?= Html::a('<i class="bi bi-plus"></i> 新增角色', ['/rbac/role/create'], ['class' => 'btn btn-sm btn-outline-success']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">权限数量</h5>
                            <h2 class="mb-0"><?= Html::encode($stats['permissionCount']) ?></h2>
                        </div>
                        <div class="text-info" style="font-size: 3rem;">
                            <i class="bi bi-key"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <?= Html::a('<i class="bi bi-list"></i> 查看列表', ['/rbac/permission/index'], ['class' => 'btn btn-sm btn-info']) ?>
                        <?= Html::a('<i class="bi bi-plus"></i> 新增权限', ['/rbac/permission/create'], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?= Html::a('<i class="bi bi-sync"></i> 扫描权限', ['/rbac/permission/scan'], [
                            'class' => 'btn btn-sm btn-outline-info',
                            'data' => [
                                'confirm' => '确定要扫描控制器并自动生成新权限吗？',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 快捷操作 -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="bi bi-bolt"></i> 快捷操作
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                    <?= Html::a('<i class="bi bi-users"></i> 管理员管理', ['/rbac/admin/index'], ['class' => 'btn btn-light w-100']) ?>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <?= Html::a('<i class="bi bi-user-tag"></i> 角色管理', ['/rbac/role/index'], ['class' => 'btn btn-light w-100']) ?>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <?= Html::a('<i class="bi bi-key"></i> 权限管理', ['/rbac/permission/index'], ['class' => 'btn btn-light w-100']) ?>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <?= Html::a('<i class="bi bi-sync"></i> 扫描权限', ['/rbac/permission/scan'], [
                        'class' => 'btn btn-light w-100',
                        'data' => [
                            'confirm' => '确定要扫描控制器并自动生成新权限吗？',
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
