<?php

namespace jzkf\rbac\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `rbac` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module with statistics
     * @return string
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $userClass = Yii::$app->user->identityClass;
        
        // 统计数据
        $stats = [
            'adminCount' => $userClass::find()->count(),
            'roleCount' => count($auth->getRoles()),
            'permissionCount' => count($auth->getPermissions()),
        ];

        return $this->render('index', [
            'stats' => $stats,
        ]);
    }
}
