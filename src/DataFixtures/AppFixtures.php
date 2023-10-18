<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Entity\Reader;
use Cassandra\Date;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $authors = [];
        for ($i = 0; $i < 5 ; $i++) {
            $authors[$i] = new Author();
            $authors[$i]->setName($faker->lastName());
            $authors[$i]->setFirstName($faker->firstName());
            $authors[$i]->setAge($faker->randomDigit());

            $manager->persist($authors[$i]);
        }

        $editors = [];
        for ($i = 0; $i < 5 ; $i++) {
            $editors[$i] = new Editor();
            $editors[$i]->setName($faker->sentence(2));
            $editors[$i]->setAddress($faker->address());
            $editors[$i]->setPhone($faker->phoneNumber());

            $manager->persist($editors[$i]);
        }

        $books = [];
        for ($i = 0; $i < 10 ; $i++) {
            $books[$i] = new Book();
            $books[$i]->setTitle($faker->text(10));
            $books[$i]->setYears($faker->numberBetween(1800, 2023));
            $books[$i]->setDescription($faker->text(300));
            $books[$i]->setPrice($faker->randomFloat(2, 1, 99));
            $books[$i]->setAuthor($authors[array_rand($authors)]);
            $books[$i]->setEditor($editors[array_rand($editors)]);

            $manager->persist($books[$i]);
        }

        $readers = [];
        for ($i = 0; $i < 10; $i++) {
            $readers[$i] = new Reader();
            $readers[$i]->setName($faker->name());
            $readers[$i]->setBirthday($faker->dateTime());
            $readers[$i]->addBook($books[array_rand($books)]);

            $manager->persist($readers[$i]);
        }

        $manager->flush();
    }
}
