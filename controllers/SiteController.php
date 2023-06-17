<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Author;
use app\models\Book;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
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
        $this->redirect('/book', 302);
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
     * Top action.
     *
     * @return Response|string
     */
    public function actionTop2($year = null)
    {
        $years = [];
        $booksByYear = [];
        $authorIds = [];
        $authors = [];

        foreach (Book::find()->all() as $book) {
            $years[$book->year] = empty($years[$book->year]) ? 1 : $years[$book->year] + 1;
        }

        if ($year) {
            $booksByYear = Book::find()->where(['year' => $year])->all();
        }

        foreach ($booksByYear as $book) {
            foreach ($book->getRelation('authors')->all() as $author) {
                $authorIds[$author['id']] = empty($authorIds[$author['id']]) ? 1 : $authorIds[$author['id']] + 1;
            }
        }

        arsort($authorIds);

        foreach ($authorIds as $id => $books) {
            $authors[$books] = Author::findOne($id);
        }
        
        return $this->render('top', [
            'year' => $year,
            'years' => $years,
            'booksByYear' => $booksByYear,
            'authors' => $authors,
        ]);
    }

    public function actionTop($year = null)
    {
        // Получаем все года, в которые были выпущены книги.
        $years = Book::find()
            ->select('year')
            ->distinct()
            ->orderBy('year ASC')
            ->column();
    
        $authors = [];
        
        // Если год был указан, получаем топ 10 авторов за этот год.
        if ($year) {
            $authors = $this->getTopAuthors($year);
        }

        // \yii\helpers\VarDumper::dump($authors, 10, 1); exit;
    
        return $this->render('top', [
            'year' => $year,
            'years' => $years,
            'authors' => $authors,
        ]);
    }   

    public function actionTop1($year = null)
    {
        $bookQuery = Book::find();

        // Get book count by year
        $years = $bookQuery
            ->select(['year', 'count(*) as cnt'])
            ->groupBy('year')
            ->orderBy('year')
            ->indexBy('year')
            ->column();

        // Get books by selected year
        $booksByYear = $year ? $bookQuery->where(['year' => $year])->all() : [];

        // Get author count by author ID
        $authorIds = [];
        if ($booksByYear) {
            $authorIds = (new \yii\db\Query())
                ->select(['author_id', 'count(*) as cnt'])
                ->from('author_book')
                ->where(['book_id' => ArrayHelper::getColumn($booksByYear, 'id')])
                ->groupBy('author_id')
                ->orderBy(['cnt' => SORT_DESC])
                ->indexBy('author_id')
                ->column();
        }

        // Get authors by ID
        $authors = empty($authorIds) ? [] : Author::find()->where(['id' => array_keys($authorIds)])->indexBy('id')->all();

        return $this->render('top', [
            'year' => $year,
            'years' => $years,
            'booksByYear' => $booksByYear,
            'authors' => $authors,
        ]);
    }

    public function getTopAuthors($year)
    {
        $rows = (new \yii\db\Query())
            ->select(['author_id', 'COUNT(author_book.book_id) AS book_count'])
            ->from('author_book')
            ->innerJoin('books', 'books.id = author_book.book_id')
            ->where(['books.year' => $year])
            ->groupBy('author_id')
            ->orderBy(['book_count' => SORT_DESC])
            ->limit(10)
            ->all();
    
        $authors = [];
        foreach ($rows as $row) {
            $author = Author::findOne($row['author_id']);
            $authors[] = [
                'author' => $author,
                'book_count' => $row['book_count'],
            ];
        }
    
        return $authors;
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
}
