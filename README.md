# Yii2 Simple SSE Demo

This project is to demonstrate a simple example of Server Side Event php implementation usage on Yii2, based on [Yii2 SEE](https://github.com/odannyc/yii2-sse) extension.

# Yii2 usuario extension

1- Install the extension:
```
composer require 2amigos/yii2-usuario:~1.5.1
```
or add this to `composer.json`:
```
"2amigos/yii2-usuario": "~1.5.1"
```
then do `composer update`
2- Configure the app:
at `config/console.php` (and maybe at `config/web.php`):
```php
    'components' => [
      'authManager' => [
          'class' => 'yii\rbac\DbManager',
          // uncomment if you want to cache RBAC items hierarchy
          // 'cache' => 'cache',
      ],
    ]
```
3- Run the migrations:
```
./yii migrate --migrationNamespaces=Da\\User\\Migration --interactive=0
./yii migrate --migrationPath=@yii/rbac/migrations --interactive=0
```

4 - Configure `user` module (in `config/web.php`):
```php
], // end of components
'modules' => [
  'user' => [
      'class' => Da\User\Module::class,
      // ...other configs from here: [Configuration Options](installation/configuration-options.md), e.g.
      // 'administrators' => ['admin'], // this is required for accessing administrative actions
      // 'generatePasswords' => true,
      // 'switchIdentitySessionKey' => 'myown_usuario_admin_user_key',
  ]
], // modules
```
and disabled (or remove) default user model config:
```php
'components' => [
        ...
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        ...
]
```

###  Create users (Method N°1):

1- You can create users manually at `web/index.php?r=user/registration/register`.

2- Confirm users manually using commands (easy way):
You need to add this module to your `config/console.php`:
```php
// This allows to use commands: `user/create`, `user/confirm`...
'modules' => [
    'user' =>  Da\User\Module::class,
],
```

3- Then you you'll be able to confirm users using `./yii user/confirm <email|username>`, e.g.:
```
./yii user/confirm admin
```
P.S. You can use Method N°2 (migrations to set user's role).

Notes:
Now you should be able to:
- Login at `/web/index.php?r=user/security/login`.
- Start using user management at: `/web/index.php/?user/admin`.
- Use commands to create and confirm users using the format `./yii user/create <email> <username> [password] [role]`, e.g.:
```
./yii user/create admin@example.com admin123 admin123 role_admin
./yii user/confirm user123
./yii user/create user123@example.com user123 user123 role_user
./yii user/confirm user123
./yii user/delete user123
```

### OPTIONAL: Create users using migrations (Method N°2):
1- Create a new migration to create a user admin, let's call it `create_admin_user`:
```
./yii migrate/create create_admin_user
```
2- Add the following instructions to this migration:
```php
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
```
3- Execute this migration:
```
./yii migrate
```

## Access as Administrator

You can allow any user to be able to login as an administrator by adding it's username to `administrators` array (in `config/web.php`):
```php
], // end of components
'modules' => [
  'user' => [
      'class' => Da\User\Module::class,    
      'administrators' => ['admin123'], // this is required for accessing administrative actions
      ...
  ]
], // modules
```


## Available Actions
| Action      | Description |
| ----------- | ----------- |
| /user/registration/register      | Displays registration form       |
| /user/registration/resend   | Displays resend        |
| /user/registration/connect   | Connect a social network account        |
| /user/registration/confirm   | Confirms a user        |
| /user/security/login   | Displays login form        |
| /user/security/logout   | Logs the user out        |
More actions at [Available Actions
](https://yii2-usuario.readthedocs.io/en/latest/installation/available-actions/).
