<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var \yii\rbac\Permission $permission */

$this->title = $permission->name;
$this->params['breadcrumbs'][] = ['label' => '权限管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-permission-view">

    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <?= Html::a('编辑', ['update', 'name' => $permission->name], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('删除', ['delete', 'name' => $permission->name], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '确定要删除此权限吗？',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $permission,
                'attributes' => [
                    'name',
                    'description',
                    [
                        'label' => '类型',
                        'value' => $permission->type === \yii\rbac\Item::TYPE_ROLE ? '角色' : '权限',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
