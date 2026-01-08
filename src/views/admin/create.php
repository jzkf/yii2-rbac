<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var array $allRoles */

$this->title = '新增管理员';
$this->params['breadcrumbs'][] = ['label' => '管理员角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-admin-create">

    <div class="card">
        <div class="card-header">
            <i class="bi bi-user-plus"></i> <?= Html::encode($this->title) ?>
        </div>
        <div class="card-body">
            <?= $this->render('_form', [
                'user' => $user,
                'allRoles' => $allRoles,
                'assignedRoles' => [],
            ]) ?>
        </div>
    </div>

</div>
