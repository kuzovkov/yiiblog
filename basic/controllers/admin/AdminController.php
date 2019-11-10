<?php

namespace app\controllers\admin;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\base\Module;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Posts;
use app\models\Courses;
use app\models\Reviews;
use app\models\Sites;
use app\models\Minicourses;
use app\models\SiteForm;
use app\models\SearchForm;

class AdminController extends Controller
{


    public function __construct($id, Module $module, array $config=[])
    {
        parent::__construct($id, $module, $config);
        $this->layout = 'admin';
    }


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
	
	public function beforeAction($action)
	{

	    if ($action->id !== 'login' && Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        return true;
	}

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
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {

        $this->layout= 'main';
        if (!Yii::$app->user->isGuest) {
            //return $this->goHome();
            return $this->redirect(Yii::$app->urlManager->createUrl(['admin/admin/index']));
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            //return $this->goBack();

            return $this->redirect(Yii::$app->urlManager->createUrl(['admin/admin/index']));
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

    public function actionIndex()
    {

        return $this->render('index', [

        ]);
    }



}
