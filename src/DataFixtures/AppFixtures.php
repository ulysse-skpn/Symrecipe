<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use DateTimeInterface;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Validator\Constraints\Time;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Users
        $users = []; 
        for ($i=0; $i < 10; $i++) 
        { 
            $user = new User();
            $user->setEmail($this->faker->email())
                ->setFullName($this->faker->name())
                ->setRoles(["ROLE_USER"])
                ->setPseudo(mt_rand(0,1) === 1 ? $this->faker->firstName() : null)
                ->setPlainPassword('password');

            $users[] = $user;

            $manager->persist($user);
        }

        // Ingredients
        $ingredients = [];
        for ($i=0; $i <= 50; $i++) 
        { 
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->words(3,true))
                        ->setPrice(mt_rand(0,100))
                        ->setUser($users[mt_rand(0 , count($users) - 1)]);

            $ingredients[] = $ingredient;

            $manager->persist($ingredient);
        }

        // Recipes
        for ($i=0; $i <= 15; $i++) 
        { 
            $recipe = new Recipe();
            $recipe->setName($this->faker->words(3,true))
                    ->setTime( new \DateTime() )
                    ->setNbPerson(mt_rand(0,1) === 1 ? mt_rand(1,50) : null)
                    ->setDifficulty( mt_rand(0,1) === 1 ? strval(mt_rand(1,5)) : null )
                    ->setInstructions( $this->faker->text(300) )
                    // ->setIngredientsList( $this->faker->words )
                    ->setPrice(mt_rand(0,1) === 1 ? mt_rand(0,1000) : null)
                    ->setBookmark( mt_rand(0,1) === 0 ? false : true )
                    ->setIsPublic( mt_rand(0,1) === 0 ? false : true )
                    ->setUser($users[mt_rand(0 , count($users) - 1)]);

            for ($k=0; $k < mt_rand(5,15) ; $k++) 
            { 
                $recipe->addIngredientsList($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
