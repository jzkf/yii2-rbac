<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \yii\rbac\Role $role */

$this->title = '编辑角色: ' . $role->name;
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $role->name, 'url' => ['view', 'name' => $role->name]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="rbac-role-update">

    <?= $this->render('_form', [
        'role' => $role,
    ]) ?>

</div>
