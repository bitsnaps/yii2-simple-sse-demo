<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'FS65GSG7YF69RTF08HF755D6Z7SD09UVHSD7GYSFPVOCXHVOCXV8V7T6XV6745D1',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
/*        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
*/
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'sse' => [
          'class' => \odannyc\Yii2SSE\LibSSE::class
        ],
        /*'authManager' => [
            'class' => 'yii\rbac\DbManager',
            // uncomment if you want to cache RBAC items hierarchy
            // 'cache' => 'cache',
        ],*/
    ], // components
    'modules' => [
      'user' => [
          'class' => Da\User\Module::class,
          // default Configuration Options values:
          'enableRegistration' => true, //Setting this attribute allows the registration process. If you set it to false, the module won't allow users to register by throwing a NotFoundHttpException if the RegistrationController::actionRegister() is accessed.
          'enableEmailConfirmation' => true, //If true, the module will send an email with a confirmation link that user needs to click through to complete its registration process.
          'enableFlashMessages' => true, //If true views will display flash messages. Disable this if you want to handle messages display in your views.
          'enableSwitchIdentities' => true, //If true allows switching identities for the admin user.
          'generatePasswords' => true, //If true the password field will be hidden on the registration page and passwords will be generated automatically and sent to the user via email.
          'allowUnconfirmedEmailLogin' => false, //If true it will allow users to login with unconfirmed emails.
          'allowPasswordRecovery' => true, //If true it will enable password recovery process.
          'allowAdminPasswordRecovery' => true, //If true it will enable administrator to send a password recovery email to a user.
          'maxPasswordAge' => null, //If set to an integer value it will check user password age. If the days since last password change are greater than this configuration value user will be forced to change it.
          'allowAccountDelete' => false, //If true users will be able to remove their own accounts.
          'rememberLoginLifespan' => 1209600, // Configures the time length in seconds a user will be remembered without the need to login again. The default time is 2 weeks.
          'emailChangeStrategy' => Da\User\Contracts\MailChangeStrategyInterface::TYPE_DEFAULT, //Configures one of the three ways available to change user's password
          'tokenConfirmationLifespan' => 86400, //Configures the time length in seconds a confirmation token is valid. The default time is 24 hours.
          'tokenRecoveryLifespan' => 21600, //Configures the time length in seconds a recovery token is valid. The default time is 6 hours.
          // This value has been changed:
          'administrators' => ['admin123'], // this is required for accessing administrative actions
          'administratorPermissionName' => null, //Configures the permission name for administrators
          'prefix' => 'user', //Configures the URL prefix for the module.
          'enableTwoFactorAuthentication' => false, //Setting this attribute will allow users to configure their login process with two-factor authentication.
          'twoFactorAuthenticationCycles' => 1, //By default, Google Authenticator App for two-factor authentication cycles in periods of 30 seconds. In order to allow a bigger period so to avoid out of sync issues.
          'enableGdprCompliance' => false, //Setting this attribute enables a serie of measures to comply with EU GDPR regulation, like data consent, right to be forgotten and data portability.
          'gdprPrivacyPolicyUrl' => null, //The link to privacy policy. This will be used on registration form as "read our pivacy policy". It must follow the same format as yii\helpers\Url::to
          'gdprExportProperties' => [
            'email',
            'username',
            'profile.public_email',
            'profile.name',
            'profile.gravatar_email',
            'profile.location',
            'profile.website',
            'profile.bio'
          ], //An array with the name of the user identity properties to be included when user request download of his data. Names can include relations like profile.name.
          'gdprAnonymizePrefix' => 'GDPR',
          'consoleControllerNamespace' => 'Da\User\Command', //Allows customization of the console application controller namespace for the module.
          'controllerNamespace' => 'Da\User\Controller', //Allows customization of the web application controller namespace for the module.
          'classMap' => [], //Configures the definitions of the classes as they have to be override. For more information see Overriding Classes.
          'routes' => [
              '<id:\d+>' => 'profile/show',
              '<action:(login|logout)>' => 'security/<action>',
              '<action:(register|resend)>' => 'registration/<action>',
              'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
              'forgot' => 'recovery/request',
              'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset'
          ], //Configures the definitions of the classes as they have to be override. For more information see Overriding Classes.
          'viewPath' => '@Da/User/resources/views', // Configures the root directory of the view files.
          'restrictUserPermissionAssignment' => false, // If false, allow the assignment of both roles and permissions to users. Set to true to restrict user assignments to roles only.
          // v1.5.2
          // 'disableIpLogging' => false, // If true registration and last login IPs are not logged into users table, instead a dummy 127.0.0.1 is used
          // ...other configs from here: [Configuration Options](installation/configuration-options.md), e.g.
          // 'switchIdentitySessionKey' => 'myown_usuario_admin_user_key', //Configures the name of the session key that will be used to hold the original admin identifier.
      ]
    ], // modules
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
