<?php

namespace jzkf\rbac\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\models\User;
use jzkf\rbac\components\PermissionHelper;
use jzkf\rbac\components\BackendMenuHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AdminController implements the admin role assignment actions.
 */
class AdminController extends Controller
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
                    'ban' => ['POST'],
                    'unban' => ['POST'],
                    // assign 和 update 需要支持 GET（显示表单）和 POST（提交表单）
                ],
            ],
        ];
    }

    /**
     * Lists all admins with their roles.
     * @return string
     */
    public function actionIndex()
    {
        // 这里需要根据实际项目调整用户模型
        $userClass = Yii::$app->user->identityClass;
        $users = $userClass::find()->all();
        
        $auth = Yii::$app->authManager;
        $userRoles = [];
        
        foreach ($users as $user) {
            $userRoles[$user->id] = array_keys($auth->getAssignments($user->id));
        }

        return $this->render('index', [
            'users' => $users,
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Creates a new admin user.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $userClass = Yii::$app->user->identityClass;
        $user = new $userClass();
        $user->scenario = 'create';
        $auth = Yii::$app->authManager;
        $allRoles = $auth->getRoles();

        if ($this->request->isPost) {
            if ($user->load($this->request->post())) {
                // 设置密码
                if (empty($user->password)) {
                    $user->addError('password', '创建用户时密码不能为空');
                } else {
                    $user->setPassword($user->password);
                    $user->generateAuthKey();
                }
                
                // 设置默认状态为启用
                if (empty($user->status)) {
                    $user->status = User::STATUS_ACTIVE;
                }
                
                if (!$user->hasErrors() && $user->save()) {
                    // 分配角色
                    $roles = $this->request->post('roles', []);
                    foreach ($roles as $roleName) {
                        $role = $auth->getRole($roleName);
                        if ($role) {
                            $auth->assign($role, $user->id);
                        }
                    }
                    
                    // 清除用户权限缓存和菜单缓存
                    PermissionHelper::clearCache($user->id);
                    BackendMenuHelper::clearCache($user->id);
                    
                    Yii::$app->session->setFlash('success', '管理员创建成功');
                    return $this->redirect(['index']);
                }
            }
            
            if ($user->hasErrors()) {
                Yii::$app->session->setFlash('error', implode("\n", $user->getErrorSummary(true)));
            }
        } else {
            $user->loadDefaultValues();
        }

        return $this->render('create', [
            'user' => $user,
            'allRoles' => $allRoles,
            'assignedRoles' => [], // 新建用户，没有已分配的角色
        ]);
    }

    /**
     * Updates an existing admin user.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param int $id User ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $userClass = Yii::$app->user->identityClass;
        $user = $userClass::findOne($id);
        
        if (!$user) {
            throw new NotFoundHttpException('用户不存在');
        }

        $user->scenario = 'update';
        $auth = Yii::$app->authManager;
        $allRoles = $auth->getRoles();
        $assignedRoles = array_keys($auth->getAssignments($id));

        if ($this->request->isPost) {
            if ($user->load($this->request->post())) {
                // 更新密码（如果提供了新密码）
                if (!empty($user->password)) {
                    $user->setPassword($user->password);
                }
                
                if (!$user->hasErrors() && $user->save()) {
                    // 更新角色分配
                    $roles = $this->request->post('roles', []);
                    
                    // Remove all existing assignments
                    $auth->revokeAll($id);
                    
                    // Assign new roles
                    foreach ($roles as $roleName) {
                        $role = $auth->getRole($roleName);
                        if ($role) {
                            $auth->assign($role, $id);
                        }
                    }
                    
                    // 清除用户权限缓存和菜单缓存
                    PermissionHelper::clearCache($id);
                    BackendMenuHelper::clearCache($id);
                    
                    Yii::$app->session->setFlash('success', '管理员信息更新成功');
                    return $this->redirect(['index']);
                }
            }
            
            if ($user->hasErrors()) {
                Yii::$app->session->setFlash('error', implode("\n", $user->getErrorSummary(true)));
            }
        } else {
            $user->loadDefaultValues();
        }

        return $this->render('update', [
            'user' => $user,
            'allRoles' => $allRoles,
            'assignedRoles' => $assignedRoles,
        ]);
    }

    /**
     * Ban an admin user (set status to inactive).
     * @param int $id User ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionBan($id)
    {
        $userClass = Yii::$app->user->identityClass;
        $user = $userClass::findOne($id);
        
        if (!$user) {
            throw new NotFoundHttpException('用户不存在');
        }

        // 不能封禁自己
        if ($user->id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', '不能封禁自己');
            return $this->redirect(['index']);
        }

        $user->status = User::STATUS_INACTIVE;
        if ($user->save(false)) {
            // 清除用户权限缓存和菜单缓存
            PermissionHelper::clearCache($id);
            BackendMenuHelper::clearCache($id);
            
            Yii::$app->session->setFlash('success', '管理员已封禁');
        } else {
            Yii::$app->session->setFlash('error', '封禁失败');
        }

        return $this->redirect(['index']);
    }

    /**
     * Unban an admin user (set status to active).
     * @param int $id User ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUnban($id)
    {
        $userClass = Yii::$app->user->identityClass;
        $user = $userClass::findOne($id);
        
        if (!$user) {
            throw new NotFoundHttpException('用户不存在');
        }

        $user->status = User::STATUS_ACTIVE;
        if ($user->save(false)) {
            // 清除用户权限缓存和菜单缓存
            PermissionHelper::clearCache($id);
            BackendMenuHelper::clearCache($id);
            
            Yii::$app->session->setFlash('success', '管理员已启用');
        } else {
            Yii::$app->session->setFlash('error', '启用失败');
        }

        return $this->redirect(['index']);
    }

    /**
     * Assign roles to admin
     * @param int $id User ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionAssign($id)
    {
        $userClass = Yii::$app->user->identityClass;
        $user = $userClass::findOne($id);
        
        if (!$user) {
            throw new NotFoundHttpException('用户不存在');
        }

        $auth = Yii::$app->authManager;

        if ($this->request->isPost) {
            $roles = $this->request->post('roles', []);
            
            // Remove all existing assignments
            $auth->revokeAll($id);
            
            // Assign new roles
            foreach ($roles as $roleName) {
                $role = $auth->getRole($roleName);
                if ($role) {
                    $auth->assign($role, $id);
                }
            }
            
            // 清除用户权限缓存和菜单缓存
            PermissionHelper::clearCache($id);
            BackendMenuHelper::clearCache($id);
            
            Yii::$app->session->setFlash('success', '权限设置成功');
            return $this->redirect(['index']);
        }

        $allRoles = $auth->getRoles();
        $assignedRoles = array_keys($auth->getAssignments($id));

        return $this->render('assign', [
            'user' => $user,
            'allRoles' => $allRoles,
            'assignedRoles' => $assignedRoles,
        ]);
    }
}
