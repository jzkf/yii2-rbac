<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var array $allRoles */
/** @var array $assignedRoles */

$this->title = '编辑管理员: ' . $user->username;
$this->params['breadcrumbs'][] = ['label' => '管理员角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-admin-update">

    <div class="card">
        <div class="card-header">
            <i class="bi bi-user-edit"></i> <?= Html::encode($this->title) ?>
        </div>
        <div class="card-body">
            <?= $this->render('_form', [
                'user' => $user,
                'allRoles' => $allRoles,
                'assignedRoles' => $assignedRoles,
            ]) ?>
        </div>
    </div>

</div>
