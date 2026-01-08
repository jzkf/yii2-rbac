<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \yii\rbac\Role $role */

$this->title = '新增角色';
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-role-create">

    <?= $this->render('_form', [
        'role' => $role,
    ]) ?>

</div>
