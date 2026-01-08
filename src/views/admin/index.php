<?php

use app\models\User;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $users */
/** @var array $userRoles */

$this->title = '管理员角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-admin-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-normal"><?= Html::encode($this->title) ?></h3>
        <div>
            <?= Html::a('<i class="bi bi-plus"></i> 新增管理员', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>邮箱</th>
                    <th>状态</th>
                    <th>角色</th>
                    <th class="text-right">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">暂无数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td><?= Html::encode($user->username) ?></td>
                            <td><?= Html::encode($user->email) ?></td>
                            <td>
                                <?php
                                $statuses = User::statuses();
                                echo $statuses[$user->status] ?? '';
                                ?>
                            </td>
                            <td>
                                <?php
                                $roles = $userRoles[$user->id] ?? [];
                                if (empty($roles)) {
                                    echo '<span class="text-muted">未分配</span>';
                                } else {
                                    echo Html::encode(implode(', ', $roles));
                                }
                                ?>
                            </td>
                            <td class="text-nowrap text-right">
                                <?= Html::a('<i class="bi bi-edit"></i> 编辑', ['update', 'id' => $user->id], ['class' => 'btn btn-sm btn-warning']) ?>
                                <?= Html::a('<i class="bi bi-user-tag"></i> 分配角色', ['assign', 'id' => $user->id], ['class' => 'btn btn-sm btn-primary']) ?>
                                <?php if ($user->status == User::STATUS_ACTIVE): ?>
                                    <?= Html::a('<i class="bi bi-ban"></i> 封禁', ['ban', 'id' => $user->id], [
                                        'class' => 'btn btn-sm btn-danger',
                                        'data' => [
                                            'confirm' => '确定要封禁此管理员吗？',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                <?php else: ?>
                                    <?= Html::a('<i class="bi bi-check"></i> 启用', ['unban', 'id' => $user->id], [
                                        'class' => 'btn btn-sm btn-success',
                                        'data' => [
                                            'confirm' => '确定要启用此管理员吗？',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
