<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $name
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'authors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getBooks()
    {
        return $this->hasMany(Book::className(), ['id' => 'book_id'])
            ->viaTable('author_book', ['author_id' => 'id']);
    }

    public function getBooksByYear() 
    {
        $books = $this->hasMany(Book::className(), ['id' => 'book_id'])
            ->viaTable('author_book', ['author_id' => 'id'])
            ->orderBy('year')
            ->all();

        $booksByYear = [];
        foreach ($books as $book) {
            $booksByYear[$book->year][] = $book;
        }

        return $booksByYear;
    }
}
