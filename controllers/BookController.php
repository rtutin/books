<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use app\models\Guest;
use app\models\Author;
use app\models\BookSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\httpclient\Client;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['create', 'update', 'view', 'delete'],
                    'rules' => [
                        [
                            'actions' => ['create', 'update', 'view', 'delete'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => ['view'],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($this->request->isPost) {    
            $model->load($this->request->post());
            $this->imageHandler($model);

            if ($model->save()) {
                $this->authorHandler($model, $this->request->post(), true);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $this->getAuthors(),
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load($this->request->post());

        if ($this->request->isPost) {
            $this->imageHandler($model);
            $this->authorHandler($model, $this->request->post());

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => $this->getAuthors(),
            'selectedAuthors' => $this->getSelectedAuthors($model),
        ]);
    }

    private function imageHandler(Book $model)
    {
        $img = UploadedFile::getInstance($model, 'img');

        if ($img) {
            $path = '/uploads/' . $img->baseName . '.' . $img->extension;
            $img->saveAs('/var/www/books/web' . $path);
            $model->img = $path;
        } else {
            $model->img = $model->getOldAttribute('img');
        }
    }

    private function authorHandler(Book $model, $data, $create = false)
    {
        foreach ($model->getRelation('authors')->all() as $author) {
            $model->unlink('authors', $author, true);
        }

        foreach ($data['Book']['authors'] as $authorId) {
            $author = Author::findOne($authorId);
            $model->link('authors', $author);

            if ($create) {
                $guests = Guest::find()->where(['author_id' => $authorId])->all();

                foreach ($guests as $guest) {
                    $client = new Client();
                    $res = $client->get(
                        'https://smspilot.ru/api.php?send=' . 
                        $author->name . ' has released a new book' . 
                        '&to=' . $guest->phone . 
                        '&apikey=' . Yii::$app->params['smspilotApiKey'] . 
                        '&format=v')->send();
                }
            }
        }
    }

    private function getAuthors()
    {
        $authors = [];

        foreach (Author::find()->asArray()->all() as $author) {
            $authors[$author['id']] = $author['name'];
        }

        return $authors;
    }

    private function getSelectedAuthors($model)
    {
        $authors = [];

        foreach ($model->getRelation('authors')->all() as $author) {
            $authors[$author['id']] = ['selected' => 'selected'];
        }

        return $authors;
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
