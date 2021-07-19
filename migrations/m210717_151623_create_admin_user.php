<?php

use yii\db\Migration;

/**
 * Class m210717_151623_create_admin_user
 */
class m210717_151623_create_admin_user extends Migration
{

  public function safeUp()
  {
      $auth = Yii::$app->authManager;

      // create a role named "administrator"
      $administratorRole = $auth->createRole('admin');
      $administratorRole->description = 'Administrator';
      $auth->add($administratorRole);

      // create permission for certain tasks
      $permission = $auth->createPermission('user-management');
      $permission->description = 'User Management';
      $auth->add($permission);

      // let administrators do user management
      $auth->addChild($administratorRole, $auth->getPermission('user-management'));

      // create user "admin" with password "admin123"
      $user = new \Da\User\Model\User([
          'scenario' => 'create',
          'email' => "admin123@example.com",
          'username' => "admin123",
          'password' => "admin123"  // >6 characters!
      ]);
      $user->confirmed_at = time();
      $user->save();

      // assign role to our admin-user
      $auth->assign($administratorRole, $user->id);
  }

  public function safeDown()
  {
      $auth = Yii::$app->authManager;

      // delete permission
      $auth->remove($auth->getPermission('user-management'));

      // delete admin-user and administrator role
      $administratorRole = $auth->getRole("administrator");
      $user = \Da\User\Model\User::findOne(['username'=>"admin123"]);
      $auth->revoke($administratorRole, $user->id);
      $user->delete();
      $auth->remove($administratorRole);
  }

}
