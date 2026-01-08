<?php

namespace jzkf\rbac\components;

use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
 * 全局权限访问控制过滤器
 * 用于拦截管理后台的所有请求，检查用户权限
 * 
 * 参考 mdmsoft/yii2-admin 的 AccessControl 模式
 */
class AccessControl extends ActionFilter
{
    /**
     * @var array 允许访问的路由列表（不需要权限检查）
     * 支持通配符，例如：'site/*', 'debug/*'
     */
    public $allowActions = [];

    /**
     * @var array 需要拦截的后台路由前缀
     * 默认拦截 /backend/* 和 /rbac/*
     */
    public $backendPrefixes = ['/backend', '/rbac'];

    /**
     * @var callable 自定义权限检查回调函数
     * 函数签名: function($action) { return bool; }
     * 返回 true 表示允许访问，false 表示拒绝
     */
    public $checkAccess;

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $route = $this->getRoute($action);
        
        // 检查是否在允许列表中
        if ($this->isAllowed($route)) {
            return parent::beforeAction($action);
        }

        // 检查是否是后台路由
        if (!$this->isBackendRoute($route)) {
            // 非后台路由，允许访问
            return parent::beforeAction($action);
        }

        // 后台路由需要权限检查
        $user = Yii::$app->user;
        
        // 如果用户未登录，要求登录
        if ($user->isGuest) {
            $user->loginRequired();
            return false;
        }

        // 如果是超级管理员，默认拥有所以权限
        if ($user->can('super_admin')) {
            return parent::beforeAction($action);
        }

        // 检查权限
        if (!$this->checkAccess($action, $user, $route)) {
            $this->denyAccess($user);
            return false;
        }

        return parent::beforeAction($action);
    }

    /**
     * 获取路由
     * 
     * @param \yii\base\Action $action
     * @return string
     */
    protected function getRoute($action)
    {
        $route = $action->controller->getRoute();
        
        // 处理模块路由
        if ($action->controller->module && $action->controller->module->id !== Yii::$app->id) {
            $route = '/' . $action->controller->module->id . '/' . $action->controller->id . '/' . $action->id;
        } else {
            $route = '/' . $route;
        }
        
        return $route;
    }

    /**
     * 检查路由是否在允许列表中
     * 
     * @param string $route
     * @return bool
     */
    protected function isAllowed($route)
    {
        foreach ($this->allowActions as $pattern) {
            if ($this->matchPattern($pattern, $route)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查是否是后台路由
     * 
     * @param string $route
     * @return bool
     */
    protected function isBackendRoute($route)
    {
        foreach ($this->backendPrefixes as $prefix) {
            if (strpos($route, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * 匹配路由模式（支持通配符）
     * 
     * @param string $pattern 模式，例如：'site/*', 'debug/*'
     * @param string $route 路由
     * @return bool
     */
    protected function matchPattern($pattern, $route)
    {
        // 完全匹配
        if ($pattern === $route) {
            return true;
        }

        // 通配符匹配
        if (substr($pattern, -2) === '/*') {
            $prefix = substr($pattern, 0, -1);
            if (strpos($route, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查访问权限
     * 
     * @param \yii\base\Action $action
     * @param \yii\web\User $user
     * @param string $route
     * @return bool
     */
    protected function checkAccess($action, $user, $route)
    {
        // 如果有自定义检查函数，使用自定义函数
        if ($this->checkAccess !== null) {
            return call_user_func($this->checkAccess, $action, $user, $route);
        }

        // 使用 Yii2 的权限检查机制
        // 先检查精确权限
        if ($user->can($route)) {
            return true;
        }

        // 检查通配符权限（例如：/backend/post/* 可以匹配 /backend/post/index）
        // Yii2 的 can() 方法不自动支持通配符，需要手动检查
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByUser($user->id);
        
        foreach ($permissions as $permission => $item) {
            // 检查通配符权限
            if (substr($permission, -2) === '/*') {
                $prefix = substr($permission, 0, -1);
                if (strpos($route, $prefix) === 0) {
                    return true;
                }
            }
        }

        // 检查角色权限（角色可能包含通配符权限）
        $assignments = $auth->getAssignments($user->id);
        foreach ($assignments as $assignment) {
            $role = $auth->getRole($assignment->roleName);
            if ($role) {
                $rolePermissions = $auth->getPermissionsByRole($assignment->roleName);
                foreach ($rolePermissions as $permission => $item) {
                    // 检查精确权限
                    if ($permission === $route) {
                        return true;
                    }
                    // 检查通配符权限
                    if (substr($permission, -2) === '/*') {
                        $prefix = substr($permission, 0, -1);
                        if (strpos($route, $prefix) === 0) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * 拒绝访问
     * 
     * @param \yii\web\User $user
     * @throws ForbiddenHttpException
     */
    protected function denyAccess($user)
    {
        if ($user->isGuest) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException('您没有权限执行此操作。');
        }
    }
}
