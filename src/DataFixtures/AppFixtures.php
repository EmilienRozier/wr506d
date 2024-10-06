<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use DateTimeImmutable;
use App\Entity\Actor;
use App\Entity\Movie;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Person($faker));

        $actors = $faker->actors($gender = null, $count = 190, $duplicates = false);
        $createdActors = [];
        foreach ($actors as $item) {
            $fullname = $item; //ex : Christian Bale
            $fullnameExploded = explode(' ', $fullname);

            $firstname = $fullnameExploded[0]; //ex : Christian
            $lastname = $fullnameExploded[1]; //ex : Bale

            $actor = new Actor();
            $actor->setLastname($lastname);
            $actor->setFirstname($firstname);
            $actor->setDob($faker->dateTimeThisCentury());
            $actor->setCreatedAt(new DateTimeImmutable());

            $createdActors[] = $actor;

            $manager->persist($actor);
        }

        $faker->addProvider(new \Xylis\FakerCinema\Provider\Movie($faker));
        $movies = $faker->movies(100);
        foreach ($movies as $item) {
            $movie = new Movie();
            $movie->setTitle($item);
            $movie->setDirector($item);
            $movie->setEntries(rand(1000, 1500000));
            $movie->setCreatedAt(new DateTimeImmutable());

            shuffle($createdActors);
            $createdActorsSliced = array_slice($createdActors, 0, 4);
            foreach ($createdActorsSliced as $actor) {
                $movie->addActor($actor);
            }
            $manager->persist($movie);

            $insertedGenre = [];

            $genres = $item->movieGenres();
            foreach ($genres as $item) {
                if (!array_key_exists($item, $insertedGenre)) {
                    $genre = new Category();

                    $genre->setTitle($item);
                    $this->em->persist($genre);

                    $inserted[$item] = $genre;


                } else {
                    $inserted[$item] = $genre;
                }
            }
        }

        $manager->flush();
        return true;
    }
}
