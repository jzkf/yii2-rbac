<?php

namespace jzkf\rbac\components;

use Yii;
use jzkf\rbac\Module;

/**
 * 权限扫描器
 * 用于自动扫描控制器并生成权限
 */
class PermissionScanner
{
    /**
     * @var Module
     */
    protected $module;

    /**
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * 扫描控制器并生成权限
     * @param \yii\rbac\ManagerInterface $auth
     * @return array 返回扫描结果 ['count' => 新增权限数量, 'permissions' => 新增的权限列表]
     */
    public function scan($auth)
    {
        $count = 0;
        $permissions = [];

        foreach ($this->module->scanPaths as $path) {
            $controllersPath = Yii::getAlias($path);
            $files = glob($controllersPath . '/*Controller.php');

            foreach ($files as $file) {
                $className = basename($file, '.php');
                $namespace = $this->getNamespaceFromPath($file, $path);
                $fullClassName = $namespace . '\\' . $className;
                
                // 跳过不存在的类
                if (!class_exists($fullClassName)) {
                    continue;
                }
                
                // 获取控制器名称（去掉 Controller 后缀）
                $controllerName = str_replace('Controller', '', $className);
                $controllerLabel = $this->module->controllerNames[$controllerName] ?? $controllerName;
                
                // 使用反射获取所有 action 方法
                $reflection = new \ReflectionClass($fullClassName);
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                
                foreach ($methods as $method) {
                    // 只处理 action 开头的方法，排除 actions() 方法
                    if (strpos($method->name, 'action') !== 0 || $method->name === 'actions') {
                        continue;
                    }
                    
                    // 提取 action 名称（去掉 action 前缀，转为小写开头）
                    $actionName = str_replace('action', '', $method->name);
                    $actionName = lcfirst($actionName);
                    
                    // 生成路由
                    $routeController = $this->camelToRoute($controllerName);
                    $routePrefix = $this->getRoutePrefix($path);
                    $route = $routePrefix . '/' . $routeController . '/' . $actionName;
                    
                    // 生成描述
                    $actionDesc = $this->module->actionDescriptions[$actionName] ?? $actionName;
                    $description = $controllerLabel . $actionDesc;
                    
                    // 检查权限是否已存在
                    if ($auth->getPermission($route)) {
                        continue;
                    }
                    
                    // 创建权限
                    $permission = $auth->createPermission($route);
                    $permission->description = $description;
                    $auth->add($permission);
                    
                    $permissions[] = [
                        'name' => $route,
                        'description' => $description,
                    ];
                    $count++;
                }
            }
        }

        return [
            'count' => $count,
            'permissions' => $permissions,
        ];
    }

    /**
     * 从文件路径获取命名空间
     * @param string $filePath
     * @param string $basePath
     * @return string
     */
    protected function getNamespaceFromPath($filePath, $basePath)
    {
        $basePath = Yii::getAlias($basePath);
        $relativePath = str_replace($basePath, '', dirname($filePath));
        $relativePath = trim($relativePath, '/\\');
        
        // 默认命名空间规则
        if (strpos($basePath, 'backend') !== false) {
            return 'app\\controllers\\backend';
        } elseif (strpos($basePath, 'frontend') !== false) {
            return 'app\\controllers\\frontend';
        }
        
        return 'app\\controllers';
    }

    /**
     * 获取路由前缀
     * @param string $path
     * @return string
     */
    protected function getRoutePrefix($path)
    {
        if (strpos($path, 'backend') !== false) {
            return '/backend';
        } elseif (strpos($path, 'frontend') !== false) {
            return '/frontend';
        }
        
        return '';
    }

    /**
     * 将驼峰命名转换为路由格式（小写加横线）
     * @param string $camelCase
     * @return string
     */
    protected function camelToRoute($camelCase)
    {
        // 移除 Controller 后缀（如果存在）
        $camelCase = preg_replace('/Controller$/', '', $camelCase);
        
        // 将驼峰命名转换为小写加横线
        $route = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $camelCase));
        
        return $route;
    }
}
