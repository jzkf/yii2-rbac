<?php

namespace jzkf\rbac\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\rbac\Role;
use yii\web\Controller;
use jzkf\rbac\components\PermissionHelper;
use jzkf\rbac\components\BackendMenuHelper;
use yii\web\NotFoundHttpException;

/**
 * RoleController implements the CRUD actions for RBAC Roles.
 */
class RoleController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all Role models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        return $this->render('index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Displays a single Role model.
     * @param string $name
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($name)
    {
        return $this->render('view', [
            'role' => $this->findModel($name),
        ]);
    }

    /**
     * Creates a new Role model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $auth = Yii::$app->authManager;
        $role = $auth->createRole(null);

        if ($this->request->isPost) {
            $name = $this->request->post('name');
            $description = $this->request->post('description', '');

            if (empty($name)) {
                Yii::$app->session->setFlash('error', '角色名称不能为空');
            } elseif ($auth->getRole($name)) {
                Yii::$app->session->setFlash('error', '角色已存在');
            } else {
                $role = $auth->createRole($name);
                $role->description = $description;
                if ($auth->add($role)) {
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
            'role' => $role,
        ]);
    }

    /**
     * Updates an existing Role model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $name
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($name)
    {
        $auth = Yii::$app->authManager;
        $role = $this->findModel($name);
        $oldName = $role->name;

        if ($this->request->isPost) {
            $newName = $this->request->post('name');
            $description = $this->request->post('description', '');

            if (empty($newName)) {
                Yii::$app->session->setFlash('error', '角色名称不能为空');
            } elseif ($newName !== $oldName && $auth->getRole($newName)) {
                Yii::$app->session->setFlash('error', '角色名称已存在');
            } else {
                // Remove old role
                $auth->remove($role);
                
                // Create new role with new name
                $newRole = $auth->createRole($newName);
                $newRole->description = $description;
                $auth->add($newRole);

                // 清除权限缓存
                PermissionHelper::clearCache();

                Yii::$app->session->setFlash('success', '更新成功');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'role' => $role,
        ]);
    }

    /**
     * Deletes an existing Role model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($name)
    {
        $auth = Yii::$app->authManager;
        $role = $this->findModel($name);
        $auth->remove($role);

        // 清除权限缓存和菜单缓存
        PermissionHelper::clearCache();
        BackendMenuHelper::clearCache();

        Yii::$app->session->setFlash('success', '删除成功');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Role model based on its name.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        $auth = Yii::$app->authManager;
        if (($role = $auth->getRole($name)) !== null) {
            return $role;
        }

        throw new NotFoundHttpException('请求的页面不存在。');
    }
}
