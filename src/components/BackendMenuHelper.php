<?php

namespace jzkf\rbac\components;

use Yii;
use yii\caching\TagDependency;
use yii\helpers\Html;

/**
 * 后台菜单辅助类
 * 根据用户权限生成管理菜单
 * 
 * 参考 mdmsoft/yii2-admin 的 MenuHelper 模式
 * 
 * 使用方法：
 * ```php
 * use jzkf\rbac\components\BackendMenuHelper;
 * 
 * $menuItems = BackendMenuHelper::getAssignedMenu();
 * ```
 */
class BackendMenuHelper
{
    /**
     * 菜单配置缓存键前缀
     */
    const CACHE_TAG = 'backend_menu';

    /**
     * 获取当前用户有权限的菜单项
     * 
     * @param int|null $userId 用户ID，如果为null则使用当前登录用户
     * @param bool $refresh 是否刷新缓存
     * @return array 菜单项数组，格式符合 yii\bootstrap5\Nav widget 的要求
     */
    public static function getAssignedMenu($userId = null, $refresh = false)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        $cache = Yii::$app->cache;
        $cacheKey = [__METHOD__, $userId];

        if ($refresh || $cache === null || ($menuItems = $cache->get($cacheKey)) === false) {
            // 加载菜单配置
            $menuConfig = require Yii::getAlias('@app/config/menu.php');
            
            // 过滤菜单项，只保留用户有权限的
            $menuItems = static::filterMenuByPermission($menuConfig, $userId);
            
            // 缓存菜单（1小时）
            if ($cache !== null) {
                $cache->set($cacheKey, $menuItems, 3600, new TagDependency([
                    'tags' => [static::CACHE_TAG, static::CACHE_TAG . '_user_' . $userId],
                ]));
            }
        }

        return $menuItems;
    }

    /**
     * 根据权限过滤菜单项
     * 
     * @param array $menuConfig 菜单配置
     * @param int|null $userId 用户ID
     * @return array 过滤后的菜单项
     */
    protected static function filterMenuByPermission($menuConfig, $userId = null)
    {
        $menuItems = [];
        
        foreach ($menuConfig as $menu) {
            // 检查是否可见
            if (isset($menu['visible']) && $menu['visible'] === false) {
                continue;
            }
            
            // 检查权限（使用 Yii::$app->user->can()，与 AccessControl 保持一致）
            if (isset($menu['url'])) {
                $route = static::routeToPermission($menu['url']);
                // 如果指定了用户ID，需要特殊处理；否则使用当前用户
                if ($userId !== null && $userId !== Yii::$app->user->id) {
                    if (!static::checkRoutePermission($route, $userId)) {
                        continue;
                    }
                } elseif (!Yii::$app->user->can($route)) {
                    continue;
                }
            }
            
            $item = [
                'label' => $menu['label'],
                'url' => $menu['url'],
            ];
            
            // 处理图标
            if (isset($menu['icon'])) {
                $item['label'] = Html::tag('i', '', ['class' => $menu['icon']]) . ' ' . $menu['label'];
            }
            
            // 处理子菜单
            if (isset($menu['items']) && is_array($menu['items']) && !empty($menu['items'])) {
                $subItems = static::filterMenuByPermission($menu['items'], $userId);
                
                if (!empty($subItems)) {
                    $item['items'] = $subItems;
                } else {
                    // 如果子菜单都被过滤掉了，不显示父菜单
                    continue;
                }
            }
            
            $menuItems[] = $item;
        }
        
        return $menuItems;
    }

    /**
     * 检查用户是否有路由权限
     * 
     * @param string $route 路由
     * @param int $userId 用户ID
     * @return bool
     */
    protected static function checkRoutePermission($route, $userId)
    {
        // 如果是指定用户ID且不是当前用户，需要特殊处理
        // 但通常菜单生成都是针对当前登录用户，所以这里简化处理
        // 如果需要检查其他用户的权限，可以通过 authManager 实现
        if ($userId !== Yii::$app->user->id) {
            // 对于非当前用户，使用 authManager 直接检查
            $auth = Yii::$app->authManager;
            $permissions = $auth->getPermissionsByUser($userId);
            
            // 检查直接权限
            if (isset($permissions[$route])) {
                return true;
            }
            
            // 检查通配符权限
            foreach ($permissions as $permission => $item) {
                if (substr($permission, -2) === '/*') {
                    $prefix = substr($permission, 0, -1);
                    if (strpos($route, $prefix) === 0) {
                        return true;
                    }
                }
            }
            
            return false;
        }
        
        // 当前用户直接使用 can() 方法（与 AccessControl 保持一致）
        return Yii::$app->user->can($route);
    }

    /**
     * 将路由数组转换为权限字符串
     * 
     * @param array|string $url 路由
     * @return string
     */
    protected static function routeToPermission($url)
    {
        if (is_array($url)) {
            $route = $url[0];
            // 如果路由不是以 / 开头，添加 /
            if (strpos($route, '/') !== 0) {
                $route = '/' . $route;
            }
            return $route;
        }
        
        return $url;
    }

    /**
     * 清除菜单缓存
     * 
     * @param int|null $userId 用户ID，如果为null则清除所有用户缓存
     */
    public static function clearCache($userId = null)
    {
        $cache = Yii::$app->cache;
        if ($cache === null) {
            return;
        }

        if ($userId === null) {
            TagDependency::invalidate($cache, static::CACHE_TAG);
        } else {
            TagDependency::invalidate($cache, static::CACHE_TAG . '_user_' . $userId);
        }
    }
}
