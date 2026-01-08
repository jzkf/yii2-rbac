# RBAC 模块迁移文件

## 迁移文件说明

### m260104_082249_add_rbac_module_permissions.php

此迁移文件用于将 RBAC 模块的所有权限添加到数据库中。

## 功能

迁移文件会：

1. **检查并创建角色**（如果不存在）：
   - `super_admin` - 超级管理员
   - `manager` - 管理员

2. **创建 RBAC 模块权限**：
   - Permission 控制器：index, view, create, update, delete, scan
   - Role 控制器：index, view, create, update, delete
   - RolePermission 控制器：assign
   - User 控制器：index, assign
   - Default 控制器：index

3. **分配权限**：
   - 将所有权限分配给 `super_admin` 角色
   - 将所有权限分配给 `manager` 角色

## 运行迁移

### 方式一：使用迁移路径参数

```bash
php yii migrate --migrationPath=@vendor/jzkf/yii2-rbac/src/migrations
```

### 方式二：在配置中设置迁移路径

在 `config/console.php` 中添加：

```php
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationPath' => [
            '@app/migrations',
            '@vendor/jzkf/yii2-rbac/src/migrations',
        ],
    ],
],
```

然后运行：

```bash
php yii migrate
```

## 回滚迁移

如果需要回滚此迁移：

```bash
php yii migrate/down --migrationPath=@vendor/jzkf/yii2-rbac/src/migrations
```

## 注意事项

1. 迁移文件会检查权限是否已存在，避免重复创建
2. 如果角色不存在，会自动创建
3. 回滚时会删除所有 RBAC 模块相关的权限
