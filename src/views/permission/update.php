<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \yii\rbac\Permission $permission */

$this->title = '编辑权限: ' . $permission->name;
$this->params['breadcrumbs'][] = ['label' => '权限管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $permission->name, 'url' => ['view', 'name' => $permission->name]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="rbac-permission-update">

    <?= $this->render('_form', [
        'permission' => $permission,
    ]) ?>

</div>
