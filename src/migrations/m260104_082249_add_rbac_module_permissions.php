<?php

use yii\db\Migration;

/**
 * 添加 RBAC 模块相关权限到数据库
 */
class m260104_082249_add_rbac_module_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // 获取或创建角色
        $superAdmin = $auth->getRole('super_admin');
        $manager = $auth->getRole('manager');

        if (!$superAdmin) {
            $superAdmin = $auth->createRole('super_admin');
            $superAdmin->description = '超级管理员';
            $auth->add($superAdmin);
        }

        if (!$manager) {
            $manager = $auth->createRole('manager');
            $manager->description = '管理员';
            $auth->add($manager);
        }

        // RBAC 模块权限列表
        $permissions = [
            // Permission 控制器权限
            ['/rbac/permission/index', '权限列表'],
            ['/rbac/permission/view', '查看权限'],
            ['/rbac/permission/create', '创建权限'],
            ['/rbac/permission/update', '更新权限'],
            ['/rbac/permission/delete', '删除权限'],
            ['/rbac/permission/scan', '扫描权限'],

            // Role 控制器权限
            ['/rbac/role/index', '角色列表'],
            ['/rbac/role/view', '查看角色'],
            ['/rbac/role/create', '创建角色'],
            ['/rbac/role/update', '更新角色'],
            ['/rbac/role/delete', '删除角色'],

            // RolePermission 控制器权限
            ['/rbac/role-permission/assign', '角色权限分配'],

            // Admin 控制器权限（管理员角色分配）
            ['/rbac/admin/index', '管理员角色列表'],
            ['/rbac/admin/create', '新增管理员'],
            ['/rbac/admin/update', '编辑管理员'],
            ['/rbac/admin/assign', '管理员角色分配'],
            ['/rbac/admin/ban', '封禁管理员'],
            ['/rbac/admin/unban', '启用管理员'],

            // Default 控制器权限
            ['/rbac/default/index', 'RBAC 默认页面'],
        ];

        // 创建权限并分配给角色
        foreach ($permissions as $permissionData) {
            list($name, $description) = $permissionData;

            // 检查权限是否已存在
            if ($auth->getPermission($name)) {
                continue;
            }

            // 创建权限
            $permission = $auth->createPermission($name);
            $permission->description = $description;
            $auth->add($permission);

            // 分配给超级管理员
            if (!$auth->hasChild($superAdmin, $permission)) {
                $auth->addChild($superAdmin, $permission);
            }

            // 分配给管理员
            if (!$auth->hasChild($manager, $permission)) {
                $auth->addChild($manager, $permission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        // RBAC 模块权限列表
        $permissions = [
            '/rbac/permission/index',
            '/rbac/permission/view',
            '/rbac/permission/create',
            '/rbac/permission/update',
            '/rbac/permission/delete',
            '/rbac/permission/scan',
            '/rbac/role/index',
            '/rbac/role/view',
            '/rbac/role/create',
            '/rbac/role/update',
            '/rbac/role/delete',
            '/rbac/role-permission/assign',
            '/rbac/admin/index',
            '/rbac/admin/create',
            '/rbac/admin/update',
            '/rbac/admin/assign',
            '/rbac/admin/ban',
            '/rbac/admin/unban',
            '/rbac/default/index',
        ];

        // 删除权限
        foreach ($permissions as $name) {
            $permission = $auth->getPermission($name);
            if ($permission) {
                $auth->remove($permission);
            }
        }
    }
}
