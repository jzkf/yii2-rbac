<?php

namespace jzkf\rbac\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\rbac\Permission;
use yii\web\Controller;
use jzkf\rbac\components\PermissionHelper;
use jzkf\rbac\components\BackendMenuHelper;
use yii\web\NotFoundHttpException;

/**
 * PermissionController implements the CRUD actions for RBAC Permissions.
 */
class PermissionController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'scan' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Permission models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissions();

        return $this->render('index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Displays a single Permission model.
     * @param string $name
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($name)
    {
        return $this->render('view', [
            'permission' => $this->findModel($name),
        ]);
    }

    /**
     * Creates a new Permission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission(null);

        if ($this->request->isPost) {
            $name = $this->request->post('name');
            $description = $this->request->post('description', '');

            if (empty($name)) {
                Yii::$app->session->setFlash('error', '权限名称不能为空');
            } elseif ($auth->getPermission($name)) {
                Yii::$app->session->setFlash('error', '权限已存在');
            } else {
                $permission = $auth->createPermission($name);
                $permission->description = $description;
                if ($auth->add($permission)) {
                    // 清除权限缓存和菜单缓存
                    PermissionHelper::clearCache();
                    BackendMenuHelper::clearCache();
                    Yii::$app->session->setFlash('success', '创建成功');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', '创建失败');
                }
            }
        }

        return $this->render('create', [
            'permission' => $permission,
        ]);
    }

    /**
     * Updates an existing Permission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $name
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($name)
    {
        $auth = Yii::$app->authManager;
        $permission = $this->findModel($name);
        $oldName = $permission->name;

        if ($this->request->isPost) {
            $newName = $this->request->post('name');
            $description = $this->request->post('description', '');

            if (empty($newName)) {
                Yii::$app->session->setFlash('error', '权限名称不能为空');
            } elseif ($newName !== $oldName && $auth->getPermission($newName)) {
                Yii::$app->session->setFlash('error', '权限名称已存在');
            } else {
                // Remove old permission
                $auth->remove($permission);
                
                // Create new permission with new name
                $newPermission = $auth->createPermission($newName);
                $newPermission->description = $description;
                $auth->add($newPermission);

                // 清除权限缓存和菜单缓存
                PermissionHelper::clearCache();
                BackendMenuHelper::clearCache();

                Yii::$app->session->setFlash('success', '更新成功');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'permission' => $permission,
        ]);
    }

    /**
     * Deletes an existing Permission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($name)
    {
        $auth = Yii::$app->authManager;
        $permission = $this->findModel($name);
        $auth->remove($permission);

        // 清除权限缓存和菜单缓存
        PermissionHelper::clearCache();
        BackendMenuHelper::clearCache();

        Yii::$app->session->setFlash('success', '删除成功');
        return $this->redirect(['index']);
    }

    /**
     * 扫描控制器并自动生成权限
     * @return \yii\web\Response
     */
    public function actionScan()
    {
        $auth = Yii::$app->authManager;
        $scanner = $this->module->getScanner();
        $result = $scanner->scan($auth);
        
        // 清除所有相关缓存，确保新权限立即生效
        // 1. 清除权限缓存（所有用户的权限缓存）
        PermissionHelper::clearCache();
        // 2. 清除菜单缓存（所有用户的菜单缓存）
        BackendMenuHelper::clearCache();
        // 3. 清除 Yii 缓存组件中可能存在的 RBAC 相关缓存
        if (Yii::$app->has('cache') && Yii::$app->cache !== null) {
            // 清除所有以 rbac 开头的缓存键（如果使用键前缀）
            // 注意：FileCache 不支持通配符删除，所以这里主要依赖 TagDependency
            // 如果使用其他缓存组件（如 Redis），可能需要额外的清理逻辑
        }
        
        if ($result['count'] > 0) {
            Yii::$app->session->setFlash('success', "扫描完成！新增 {$result['count']} 个权限，缓存已清理。");
        } else {
            Yii::$app->session->setFlash('info', '扫描完成！未发现新的权限，缓存已清理。');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the Permission model based on its name.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name
     * @return Permission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        $auth = Yii::$app->authManager;
        if (($permission = $auth->getPermission($name)) !== null) {
            return $permission;
        }

        throw new NotFoundHttpException('请求的页面不存在。');
    }
}
