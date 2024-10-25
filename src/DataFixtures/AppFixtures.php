<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use DateTimeImmutable;
use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Movie;
use App\Entity\User;
use DateTime;

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

        $insertedGenre = [];
        $genres = $faker->movieGenres($count = 21, $duplicates = false);

        foreach ($genres as $item) {

            if (!array_key_exists($item, $insertedGenre)) {
                $genre = new Category();
                $genre->setTitle($item);
                $genre->setCreatedAt(new DateTimeImmutable());

                $manager->persist($genre);

                $insertedGenre[$item] = $genre;
            }
        }

        $directors = $faker->directors($gender = null, $count = 140, $duplicates = false);
        foreach ($movies as $item) {
            $movie = new Movie();
            $movie->setTitle($item);
            $movie->setEntries(rand(1000, 1500000));
            $date = $faker->dateTime();
            $movie->setReleaseDate($date->format('d/m/Y'));
            $movie->setCreatedAt(new DateTimeImmutable());

            shuffle($createdActors);
            $createdActorsSliced = array_slice($createdActors, 0, 4);
            foreach ($createdActorsSliced as $actor) {
                $movie->addActor($actor);
            }

            shuffle($insertedGenre);
            $insertedGenreSliced = array_slice($insertedGenre, 0, 2);
            foreach ($insertedGenreSliced as $categorie) {
                $movie->addCategory($categorie);
            }

            shuffle($directors);
            $createdDirectorsSliced = array_slice($directors, 0, 1);
            foreach ($createdDirectorsSliced as $director) {
                // var_dump($director);
                $movie->setDirector($director);
            }

            $manager->persist($movie);
        }

        $user = new User();

        $user->setEmail('admin@admin.com');
        // password hashed is -> 'admin'
        $user->setPassword('$2y$13$1pFzf2EDRflvb9ildEfi1OoCtxK.4dD/hvDT2LC6nDJlhXrFjIt2i');
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();
        return true;
    }
}
