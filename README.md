# Yii2 RBAC Module

一个功能完整的 Yii2 RBAC（基于角色的访问控制）管理模块，提供权限管理、角色管理和权限校验功能。

## 安装

```bash
composer require jzkf/yii2-rbac
```

## 功能特性

- ✅ **权限管理**：完整的权限 CRUD 操作
- ✅ **角色管理**：完整的角色 CRUD 操作
- ✅ **角色权限分配**：为角色分配/移除权限，支持全选功能
- ✅ **管理员管理**：管理员的角色分配和用户管理
- ✅ **自动权限扫描**：自动扫描控制器并生成权限
- ✅ **权限校验组件**：提供 AccessControl 过滤器进行权限校验
- ✅ **菜单助手**：根据用户权限自动生成后台菜单
- ✅ **可配置**：支持自定义扫描路径、控制器名称映射等

## 快速开始

### 1. 在应用配置中注册模块

在 `config/web.php` 中添加：

```php
'modules' => [
    'rbac' => [
        'class' => 'jzkf\rbac\Module',
        'scanPaths' => [
            '@app/controllers/backend',
        ],
        'controllerNames' => [
            'Post' => '文章',
            'Category' => '分类',
            // ... 更多映射
        ],
        'actionDescriptions' => [
            'index' => '列表',
            'create' => '创建',
            'update' => '更新',
            'delete' => '删除',
            // ... 更多映射
        ],
    ],
],
```

### 2. 配置全局访问控制

在 `config/web.php` 中添加：

```php
'as access' => [
    'class' => 'jzkf\rbac\components\AccessControl',
    'allowActions' => [
        'site/*',           // 前台站点路由
        'debug/*',          // Debug 工具（开发环境）
        'gii/*',            // Gii 工具（开发环境）
    ],
    'backendPrefixes' => [
        '/backend',         // 后台管理路由
        '/rbac',            // RBAC 模块路由
    ],
],
```

### 3. 运行迁移（可选）

如果需要初始化权限数据，可以运行迁移文件：

```bash
php yii migrate/up --migrationPath=@yii/rbac/migrations
php yii migrate --migrationPath=@vendor/jzkf/yii2-rbac/src/migrations
```

### 4. 使用菜单助手

在布局文件中使用菜单助手：

```php
use jzkf\rbac\components\BackendMenuHelper;

$menuItems = BackendMenuHelper::getAssignedMenu();
```

## 目录结构

```
yii2-rbac/
├── src/
│   ├── components/          # 组件
│   │   ├── AccessControl.php          # 访问控制过滤器
│   │   ├── BackendMenuHelper.php      # 后台菜单助手
│   │   ├── PermissionHelper.php       # 权限助手
│   │   └── PermissionScanner.php      # 权限扫描器
│   ├── controllers/         # 控制器
│   │   ├── AdminController.php        # 管理员管理
│   │   ├── DefaultController.php      # 默认控制器（统计面板）
│   │   ├── PermissionController.php   # 权限管理
│   │   ├── RoleController.php         # 角色管理
│   │   └── RolePermissionController.php # 角色权限分配
│   ├── migrations/          # 数据库迁移
│   ├── views/               # 视图文件
│   ├── Bootstrap.php        # 引导类
│   └── Module.php           # 模块类
├── composer.json
└── README.md
```

## 使用说明

### 权限管理

访问 `/rbac/permission/index` 可以：
- 查看所有权限
- 创建新权限
- 编辑权限
- 删除权限
- **扫描权限**：自动扫描控制器并生成权限

### 角色管理

访问 `/rbac/role/index` 可以：
- 查看所有角色
- 创建新角色
- 编辑角色
- 删除角色
- **权限设置**：为角色分配权限（支持全选/取消全选）

### 管理员管理

访问 `/rbac/admin/index` 可以：
- 查看所有管理员
- 创建新管理员
- 编辑管理员信息
- 分配角色
- 封禁/启用管理员

## 权限扫描

模块提供了自动权限扫描功能，可以自动从控制器中提取 action 方法并生成权限。

访问 `/rbac/permission/scan` 或点击"扫描权限"按钮，系统会：
1. 扫描配置的 `scanPaths` 中的所有控制器
2. 提取所有 `action*` 方法
3. 生成路由格式的权限名称
4. 自动创建不存在的权限
5. 清理缓存

## 权限命名规则

权限名称使用路由格式：
- `/backend/post/index` - 文章列表
- `/backend/post/create` - 创建文章
- `/backend/post/update` - 更新文章
- `/backend/post/delete` - 删除文章

支持通配符权限：
- `/backend/post/*` - 匹配所有 `/backend/post/` 下的路由

## 缓存管理

模块使用缓存来提高性能。当权限、角色或管理员数据发生变化时，会自动清理相关缓存：
- 权限缓存
- 菜单缓存

也可以在代码中手动清理：

```php
use jzkf\rbac\components\PermissionHelper;
use jzkf\rbac\components\BackendMenuHelper;

// 清理所有权限缓存
PermissionHelper::clearCache();

// 清理所有菜单缓存
BackendMenuHelper::clearCache();
```

## 配置选项

### Module 配置

- `scanPaths` (array): 权限扫描路径，默认为 `['@app/controllers/backend']`
- `controllerNames` (array): 控制器名称映射，用于生成中文描述
- `actionDescriptions` (array): Action 描述映射，用于生成中文描述
- `defaultRoute` (string): 默认路由，默认为 `'default/index'`

### AccessControl 配置

- `allowActions` (array): 允许访问的路由列表（白名单）
- `backendPrefixes` (array): 后台路由前缀，默认 `['/backend', '/rbac']`


## 截图（Screenshots）

### RBAC 管理面板

![RBAC 管理面板](screenshot/RBAC%20管理面板%20-%20default.png)

RBAC 模块的默认首页，显示管理员数量、角色数量和权限数量的统计信息。

### 权限管理

![权限管理](screenshot/权限管理%20-%20permission.png)

权限管理页面，可以查看、创建、编辑、删除权限，以及使用"扫描权限"功能自动生成权限。

### 角色管理

![角色管理](screenshot/角色管理%20-%20role%20index.png)

角色管理页面，可以查看、创建、编辑、删除角色。

### 角色权限分配

![角色权限分配](screenshot/权限设置_%20manager(管理员)%20-%20role-permission.png)

为角色分配权限的页面，支持全选/取消全选功能，可以快速为角色分配多个权限。

### 管理员角色分配

![管理员角色分配](screenshot/角色分配_%20admin%20-%20assign.png)

为管理员分配角色的页面，可以管理管理员的角色权限。

## 许可证

BSD-3-Clause

## 支持

如有问题或建议，请提交 Issue 或 Pull Request。
