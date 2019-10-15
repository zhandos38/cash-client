<?php

namespace backend\modules\rbac\models;

use Yii;
use yii\base\Model;

/**
 * @author John Martin <john.itvn@gmail.com>
 * @since 1.0.0
 */
class AssignmentForm extends Model {

    public $userId;
    public $roles = [];
    public $permissions = [];
    public $authManager;

    /**
     * 
     * @param mixed $userId The id of user use for assign
     * @param array $config 
     */
    public function __construct($userId, $config = array()) {
        parent::__construct($config);
        $this->userId = $userId;
        $this->authManager = Yii::$app->authManager;
        foreach ($this->authManager->getPermissionsByUser($userId) as $role) {
            $this->roles[] = $role->name;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['userId'], 'required'],
            [['roles', 'permissions'], 'default'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'userId' => Yii::t('rbac', 'User ID'),
            'permissions' => 'Разрешение',
        ];
    }

    /**
     * Save assignment data
     * @return boolean whether assignment save success
     */
    public function save() {
        $this->authManager->revokeAll(intval($this->userId));
        if ($this->roles != null) {
            foreach ($this->roles as $role) {
                $this->authManager->assign($this->authManager->getPermission($role), $this->userId);
            }
        }
        return true;
    }

}
