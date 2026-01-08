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
 * RolePermissionController implements the permission assignment actions for RBAC Roles.
 */
class RolePermissionController extends Controller
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
                    // assign 需要支持 GET（显示表单）和 POST（提交表单）
                ],
            ],
        ];
    }

    /**
     * Assign permissions to a role.
     * @param string $name Role name
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the role cannot be found
     */
    public function actionAssign($name)
    {
        $auth = Yii::$app->authManager;
        $role = $this->findModel($name);

        if ($this->request->isPost) {
            $permissions = $this->request->post('permissions', []);
            
            // Remove all existing permissions
            $existingPermissions = $auth->getPermissionsByRole($name);
            foreach ($existingPermissions as $permission) {
                $auth->removeChild($role, $permission);
            }
            
            // Assign new permissions
            foreach ($permissions as $permissionName) {
                $permission = $auth->getPermission($permissionName);
                if ($permission) {
                    $auth->addChild($role, $permission);
                }
            }
            
            // 清除所有用户权限缓存和菜单缓存（因为角色权限变更影响所有拥有该角色的用户）
            PermissionHelper::clearCache();
            BackendMenuHelper::clearCache();
            
            Yii::$app->session->setFlash('success', '权限设置成功');
            return $this->redirect(['role/view', 'name' => $name]);
        }

        $allPermissions = $auth->getPermissions();
        $assignedPermissions = array_keys($auth->getPermissionsByRole($name));

        return $this->render('assign', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'assignedPermissions' => $assignedPermissions,
        ]);
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
