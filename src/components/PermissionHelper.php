<?php

namespace jzkf\rbac\components;

use Yii;
use yii\caching\TagDependency;

/**
 * 权限辅助类
 * 用于获取和管理用户权限
 */
class PermissionHelper
{
    /**
     * 获取当前用户的所有权限
     * @param int|null $userId 用户ID，如果为null则获取当前登录用户
     * @param bool $refresh 是否刷新缓存
     * @return array 权限名称数组
     */
    public static function getUserPermissions($userId = null, $refresh = false)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if ($userId === null) {
            return [];
        }

        $auth = Yii::$app->authManager;
        $cache = Yii::$app->cache;
        $cacheKey = ['rbac_permissions', $userId];

        if ($refresh || $cache === null || ($permissions = $cache->get($cacheKey)) === false) {
            $permissions = [];
            
            // 获取用户直接分配的权限和角色
            $assignments = $auth->getAssignments($userId);
            foreach ($assignments as $assignment) {
                // 检查是角色还是权限
                $role = $auth->getRole($assignment->roleName);
                if ($role) {
                    // 获取角色下的所有权限
                    $rolePermissions = $auth->getPermissionsByRole($assignment->roleName);
                    foreach ($rolePermissions as $permission) {
                        $permissions[$permission->name] = $permission;
                    }
                } else {
                    // 可能是直接分配的权限
                    $permission = $auth->getPermission($assignment->roleName);
                    if ($permission) {
                        $permissions[$permission->name] = $permission;
                    }
                }
            }

            // 获取默认角色的权限
            foreach ($auth->defaultRoles as $roleName) {
                $rolePermissions = $auth->getPermissionsByRole($roleName);
                foreach ($rolePermissions as $permission) {
                    $permissions[$permission->name] = $permission;
                }
            }

            // 缓存权限（1小时）
            if ($cache !== null) {
                $cache->set($cacheKey, $permissions, 3600, new TagDependency([
                    'tags' => ['rbac_permissions', "rbac_permissions_user_{$userId}"],
                ]));
            }
        }

        return array_keys($permissions);
    }

    /**
     * 检查用户是否有指定权限
     * @param string $permission 权限名称
     * @param int|null $userId 用户ID，如果为null则检查当前登录用户
     * @return bool
     */
    public static function hasPermission($permission, $userId = null)
    {
        if ($userId === null) {
            return Yii::$app->user->can($permission);
        }

        $permissions = static::getUserPermissions($userId);
        return in_array($permission, $permissions);
    }

    /**
     * 检查用户是否有指定路由的权限
     * 支持通配符匹配，例如：/backend/post/* 可以匹配 /backend/post/index
     * @param string $route 路由
     * @param int|null $userId 用户ID
     * @return bool
     */
    public static function hasRoutePermission($route, $userId = null)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if ($userId === null) {
            return false;
        }

        // 直接检查权限
        if (static::hasPermission($route, $userId)) {
            return true;
        }

        // 检查通配符权限
        $permissions = static::getUserPermissions($userId);
        foreach ($permissions as $permission) {
            // 如果权限以 /* 结尾，检查路由是否匹配
            if (substr($permission, -2) === '/*') {
                $prefix = substr($permission, 0, -1);
                if (strpos($route, $prefix) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 清除用户权限缓存
     * @param int|null $userId 用户ID，如果为null则清除所有用户缓存
     */
    public static function clearCache($userId = null)
    {
        $cache = Yii::$app->cache;
        if ($cache === null) {
            return;
        }

        if ($userId === null) {
            TagDependency::invalidate($cache, 'rbac_permissions');
        } else {
            TagDependency::invalidate($cache, "rbac_permissions_user_{$userId}");
        }
    }

    /**
     * 将路由数组转换为权限字符串
     * @param array|string $route 路由
     * @return string
     */
    public static function routeToPermission($route)
    {
        if (is_array($route)) {
            // 处理 Yii2 路由数组格式 ['controller/action', 'param' => 'value']
            $routeStr = $route[0];
            // 如果路由不是以 / 开头，添加 /
            if (strpos($routeStr, '/') !== 0) {
                $routeStr = '/' . $routeStr;
            }
            return $routeStr;
        }

        return $route;
    }
}
