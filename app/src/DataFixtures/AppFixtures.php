<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i < 100; $i++) {
            $description = $faker->text(3000);

            $book = new Book();
            $book
                ->setTitle($faker->sentence(3))
                ->setAuthor($faker->name)
                ->setPublishedAt($faker->dateTimeBetween('-30 years', 'now'))
                ->setDescription($description)
                ->setIsbn($faker->isbn13)
                ->setCategory($faker->randomElement($categories))
            ;
            $manager->persist($book);
        }

        $manager->flush();
    }
}
