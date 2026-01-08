# Yii2 RBAC Module

一个功能完整的 Yii2 RBAC（基于角色的访问控制）管理模块，提供权限管理、角色管理和权限校验功能。

## 功能特性

- ✅ **权限管理**：完整的权限 CRUD 操作
- ✅ **角色管理**：完整的角色 CRUD 操作
- ✅ **角色权限分配**：为角色分配/移除权限
- ✅ **用户角色分配**：为用户分配/移除角色
- ✅ **自动权限扫描**：自动扫描控制器并生成权限
- ✅ **权限校验组件**：提供 AccessControl 过滤器进行权限校验
- ✅ **可配置**：支持自定义扫描路径、控制器名称映射等

## 安装

### 方式一：作为 Composer 包安装

```bash
composer require app/rbac-module
```

### 方式二：直接使用模块

将模块目录复制到项目的 `modules` 目录下。

## 配置

### 1. 在应用配置中注册模块

在 `config/web.php` 中添加：

```php
'modules' => [
    'rbac' => [
        'class' => 'jzkf\rbac\Module',
        'scanPaths' => [
            '@app/controllers/backend',
            '@app/controllers/frontend',
        ],
        'controllerNames' => [
            'Post' => '文章',
            'Category' => '分类',
            // ... 更多映射
        ],
        'actionDescriptions' => [
            'index' => '列表',
            'create' => '创建',
            // ... 更多映射
        ],
    ],
],
```

### 2. 确保已配置 authManager

在 `config/web.php` 中配置：

```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
],
```

### 3. 运行迁移

确保已运行 RBAC 相关的数据库迁移：

```bash
php yii migrate
```

## 使用方法

### 权限管理

访问 `/rbac/permission/index` 查看所有权限。

- **扫描权限**：点击"扫描权限"按钮，自动扫描控制器并生成权限
- **新增权限**：手动创建新权限
- **编辑/删除**：管理现有权限

### 角色管理

访问 `/rbac/role/index` 查看所有角色。

- **创建角色**：创建新角色
- **分配权限**：为角色分配权限
- **编辑/删除**：管理现有角色

### 用户角色分配

访问 `/rbac/admin/index` 查看所有管理员及其角色。

- **分配角色**：为用户分配角色

### 权限校验

在控制器中使用 `AccessControl` 过滤器：

```php
use jzkf\rbac\components\AccessControl;

public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::class,
            'only' => ['create', 'update', 'delete'],
            'roles' => ['@'], // 需要登录
        ],
    ];
}
```

或者使用 RBAC 权限：

```php
public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::class,
            'only' => ['create', 'update', 'delete'],
            'roles' => ['admin', '/backend/post/create'], // 需要 admin 角色或特定权限
        ],
    ];
}
```

### 在视图中检查权限

```php
use Yii;

// 检查用户是否有权限
if (Yii::$app->user->can('/backend/post/create')) {
    echo Html::a('创建文章', ['post/create']);
}

// 检查用户是否有角色
if (Yii::$app->user->can('admin')) {
    // 管理员功能
}
```

## API 文档

### Module 类

#### 属性

- `$scanPaths` (array): 权限扫描路径列表
- `$controllerNames` (array): 控制器名称映射
- `$actionDescriptions` (array): Action 描述映射

#### 方法

- `getScanner()`: 获取权限扫描器实例

### PermissionScanner 类

#### 方法

- `scan($auth)`: 扫描控制器并生成权限
  - 参数：`$auth` - RBAC 管理器实例
  - 返回：`['count' => int, 'permissions' => array]`

### AccessControl 类

#### 属性

- `$roles` (array): 允许访问的角色列表
- `$checkAccess` (callable): 自定义权限检查回调函数

## 目录结构

```
modules/rbac/
├── Module.php                 # 模块主类
├── controllers/               # 控制器
│   ├── DefaultController.php
│   ├── PermissionController.php
│   ├── RoleController.php
│   ├── RolePermissionController.php
│   └── AdminController.php
├── components/                # 组件
│   ├── AccessControl.php     # 权限校验过滤器
│   └── PermissionScanner.php # 权限扫描器
├── views/                     # 视图文件
│   ├── permission/
│   ├── role/
│   └── user/
├── migrations/                 # 数据库迁移（可选）
├── composer.json              # Composer 配置
└── README.md                  # 说明文档
```

## 开发

### 扩展模块

可以通过继承 Module 类来扩展功能：

```php
namespace app\modules\custom;

class Module extends \jzkf\rbac\Module
{
    public function init()
    {
        parent::init();
        // 自定义初始化
    }
}
```

## 许可证

BSD-3-Clause

## 贡献

欢迎提交 Issue 和 Pull Request！
