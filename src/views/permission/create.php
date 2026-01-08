<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \yii\rbac\Permission $permission */

$this->title = '新增权限';
$this->params['breadcrumbs'][] = ['label' => '权限管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-permission-create">

    <?= $this->render('_form', [
        'permission' => $permission,
    ]) ?>

</div>
