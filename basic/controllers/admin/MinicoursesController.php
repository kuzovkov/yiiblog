<?php

namespace app\controllers\admin;

use Yii;
use app\models\Minicourses;
use app\models\MinicoursesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Module;
use yii\web\UploadedFile;

/**
 * MinicoursesController implements the CRUD actions for Minicourses model.
 */
class MinicoursesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function __construct($id, Module $module, array $config=[])
    {
        parent::__construct($id, $module, $config);
        $this->layout = 'admin';
    }

    public function beforeAction($action)
    {

        if (Yii::$app->user->isGuest) {
            $this->layout = 'main';
            return $this->redirect(Yii::$app->urlManager->createUrl(['admin/admin/login']));
        }
        return true;
    }

    /**
     * Lists all Minicourses models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MinicoursesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Minicourses model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Minicourses model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Minicourses();

        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'img');
            if ($file){
                $file->saveAs($model->relative_images_dir.'/'.$file->baseName.'.'.$file->extension);
                $model->img = $file->baseName.'.'.$file->extension;
            }
            if ($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
            else
                return $this->redirect(['error', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Minicourses model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $curr_img = $model->img;
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'img');
            if ($file){
                if ($curr_img){
                    $oldfile = $model->relative_images_dir.'/'.basename($curr_img);
                    if (file_exists($oldfile) && is_file($oldfile))
                        unlink($oldfile);
                }
                $file->saveAs($model->relative_images_dir.'/'.$file->baseName.'.'.$file->extension);
                $model->img = $file->baseName.'.'.$file->extension;
            }else{
                $model->img = basename($curr_img);
            }
            if ($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
            else
                return $this->redirect(['error', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Minicourses model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $imgfile = $model->relative_images_dir.'/'.basename($model->img);
        if (file_exists($imgfile) && is_file($imgfile))
            unlink($imgfile);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Minicourses model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Minicourses the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Minicourses::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
