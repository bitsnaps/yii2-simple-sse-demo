<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\sse\MessageEventHandler;
use Automattic\WooCommerce\Client;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
    * Create a SSE
    */
    public function actionSse()
    {
      $sse = Yii::$app->sse;

      // Optional settings
    	$sse->exec_limit = 10; //the execution time of the loop in seconds. Default: 600. Set to 0 to allow the script to run as long as possible.
    	$sse->sleep_time = 1; //The time to sleep after the data has been sent in seconds. Default: 0.5.
    	$sse->client_reconnect = 10; //the time for the client to reconnect after the connection has lost in seconds. Default: 1.
    	$sse->use_chunked_encoding = true; //Use chunked encoding. Some server may get problems with this and it defaults to false
    	$sse->keep_alive_time = 600; //The interval of sending a signal to keep the connection alive. Default: 300 seconds.
    	$sse->allow_cors = true; //Allow cross-domain access? Default: false. If you want others to access this must set to true.

      $sse->addEventListener('message', new MessageEventHandler());
      $sse->start();
      // return $sse->createResponse();
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionWoo()
    {
      /*
      $posts = Yii::$app->blog->getPosts([
          'post_status' => 'publish',
          'number' => 10
      ], ['guid', 'post_title', 'post_content']);
      $postID = Yii::$app->blog->newPost('New post', 'Hello world 2');
      */
      $posts = [];
      $postID = 0;

      $woocommerce = new Client(
          'http://127.0.0.1/woocommerce',
          'ck_b00ed47c23037e0a4472fc22b42c197585cad4f7',
          'cs_2e447bc3f81ce490ff94284ff636fd5a268f7a34',
          [
              'version' => 'wc/v3',
              'wp_api' => true, // Optional
              'verify_ssl' => false, // Optional
          ]
      );
/*
$data = [
    'name' => 'Premium Quality',
    'type' => 'simple',
    'regular_price' => '21.99',
    'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.',
    'short_description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
    'categories' => [
        [
            'id' => 9
        ],
        [
            'id' => 14
        ]
    ],
    'images' => [
        [
            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
        ],
        [
            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
        ]
    ]
];
$results = $woocommerce->post('products', $data);
render "Product: $$results->name created.";
*/

      return $this->render('index', [
        'posts' => $posts,
        'postID' => $postID,
        'woocommerce' => $woocommerce
      ]);
    }

}
