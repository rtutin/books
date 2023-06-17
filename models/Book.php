<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $name
 * @property int|null $year
 * @property string|null $isbn
 * @property string|null $img
 * @property string|null $description
 */
class Book extends \yii\db\ActiveRecord
{
    public $authors = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['year'], 'integer'],
            [['description'], 'string'],
            [['name', 'isbn'], 'string', 'max' => 255],
            [['img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
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
            'year' => 'Year',
            'isbn' => 'ISBN',
            'img' => 'Image',
            'description' => 'Description',
        ];
    }

    public function upload()
    {
        // \yii\helpers\VarDumper::dump($this, 10, 1); exit;
        if ($this->validate()) {
            $this->img->saveAs('/var/www/books/web/uploads/' . $this->img->baseName . '.' . $this->img->extension);
            return true;
        } else {
            return false;
        }
    }

    public function getAuthors()
    {
        return $this->hasMany(Author::className(), ['id' => 'author_id'])
            ->viaTable('author_book', ['book_id' => 'id']);
    }
}
