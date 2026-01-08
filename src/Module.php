<?php

namespace jzkf\rbac;

use Yii;

/**
 * RBAC module definition class
 * 
 * 提供完整的 RBAC 权限管理和权限校验功能
 * 
 * @property string $controllerNamespace 控制器命名空间
 * @property string $defaultRoute 默认路由
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'jzkf\rbac\controllers';

    /**
     * 布局文件
     */
    public $layout = '@app/views/backend/layouts/main';

    /**
     * 默认路由
     */
    public $defaultRoute = 'default/index';

    /**
     * 权限扫描路径
     * 可以配置多个路径，用于自动扫描生成权限
     */
    public $scanPaths = [
        '@app/controllers/backend',
    ];

    /**
     * 控制器名称映射（用于生成中文描述）
     */
    public $controllerNames = [
        'Post' => '文章',
        'Category' => '分类',
        'Page' => '页面',
        'Setting' => '设置',
        'User' => '管理员',
        'Role' => '角色',
        'Permission' => '权限',
        'Dict' => '字典',
        'DictItem' => '字典项',
        'Language' => '语言',
        'Log' => '日志',
        'Dashboard' => '仪表盘',
        'RolePermission' => '角色权限',
    ];

    /**
     * Action 描述映射（用于生成中文描述）
     */
    public $actionDescriptions = [
        'index' => '列表',
        'view' => '查看',
        'create' => '创建',
        'update' => '更新',
        'delete' => '删除',
        'assign' => '权限设置',
        'list' => '列表',
        'upload' => '上传',
        'validateForm' => '表单验证',
        'jsonLists' => 'JSON列表',
        'batchUpdateCount' => '批量更新',
        'crawl' => '抓取',
        'uploadDocx' => '上传文档',
        'uploadDocxValidate' => '验证文档',
        'deleteAll' => '批量删除',
        'pay' => '支付',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // 自定义初始化代码
        $this->registerTranslations();
    }

    /**
     * 注册翻译
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['rbac*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'zh-CN',
            'basePath' => '@vendor/jzkf/yii2-rbac/src/messages',
        ];
    }

    /**
     * 获取权限扫描器实例
     * @return \jzkf\rbac\components\PermissionScanner
     */
    public function getScanner()
    {
        return new \jzkf\rbac\components\PermissionScanner($this);
    }
}
