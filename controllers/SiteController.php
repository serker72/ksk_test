<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Images;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\imagine\Image;

Yii::setAlias('@common', Yii::getAlias('@webroot') . '/uploads');
Yii::setAlias('@common_web', Yii::getAlias('@web') . '/uploads');

class SiteController extends Controller
{
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

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Images::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
     * Загрузка файлов картинок
     */
    public function actionUpload()
    {
        $model = new Images;
        $fileName = 'file';
        $uploadPath = Yii::getAlias('@common').'/images/'; //это путь для сохранения
        $uploadThumbnailPath = Yii::getAlias('@common').'/images/thumbnail/'; //это путь для сохранения thumbnail
        if (isset($_FILES[$fileName])) {
            $file = UploadedFile::getInstanceByName($fileName);
            $date = strtotime('now');
            $newname = $date.'-'.rand(100,999).'.'.$file->extension;
            $model->filename = $newname;
            if(($file->saveAs($uploadPath .$newname)) and ($model->save())) {
                Image::thumbnail($uploadPath.$newname, 150, 150)->save($uploadThumbnailPath.$newname, ['quality' => 80]);
                echo Json::encode($file);
            }
        }
        return false;
    }
     
    /**
     * Finds the Images model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Images the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Images::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing Images model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        if(Yii::$app->request->isAjax) {
            return true;
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => Images::find(),
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    
    public function actionClear()
    {
        foreach (Images::find()->all() as $image) {
            $image->delete();
        }        
        
        Yii::$app->db->createCommand("UPDATE sqlite_sequence SET seq=0 WHERE name='images'")->execute();

        if(Yii::$app->request->isAjax) {
            return true;
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => Images::find(),
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    
}
