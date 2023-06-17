<?php

use yii\db\Migration;

/**
 * Class m230617_144706_seed_data
 */
class m230617_144706_seed_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $faker = \Faker\Factory::create();

        // Create 10 authors
        for ($i = 0; $i < 10; $i++) {
            $this->insert('authors', [
                'name' => $faker->name,
            ]);
        }

        // Get all author IDs
        $authorIds = (new \yii\db\Query())
            ->select('id')
            ->from('authors')
            ->column();

        // Create 100 books and link them to random authors
        for ($i = 0; $i < 1000; $i++) {
            $this->insert('books', [
                'name' => $faker->sentence,
                'year' => $faker->year,
                'isbn' => $faker->isbn13,
                'img' => $faker->imageUrl(),
                'description' => $faker->text,
            ]);

            $bookId = $this->getDb()->getLastInsertID();

            foreach ($authorIds as $authorId) {
                if (rand(0, 1)) { // 50% chance that the author is linked to the book
                    $this->insert('author_book', [
                        'author_id' => $authorId,
                        'book_id' => $bookId,
                    ]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('author_book');
        $this->delete('books');
        $this->delete('authors');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230617_144706_seed_data cannot be reverted.\n";

        return false;
    }
    */
}
