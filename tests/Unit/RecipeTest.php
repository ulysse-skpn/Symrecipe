<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Entity\Rating;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    public function getEntity(): Recipe
    {
        return (new Recipe())->setInstructions( "Instructions #1" )
                            ->setBookmark( true )
                            ->setCreatedAt( new \DateTimeImmutable() )
                            ->setUpdatedAt( new \DateTimeImmutable() )
        ;
    }

    /** @test */
    public function entityIsValid()
    {
        self::bootKernel([
            'debug' => false
        ]);

        $container = static::getContainer();

        $recipe = $this->getEntity();
        $recipe->setName("Recette #1");

        $errors = $container->get("validator")->validate($recipe);

        $this->assertCount(0, $errors);
    }

    /** @test */
    public function invalidName()
    {
        self::bootKernel([
            'debug' => false
        ]);

        $container = static::getContainer();

        $recipe = $this->getEntity();
        $recipe->setName("");

        $errors = $container->get("validator")->validate($recipe);

        $this->assertCount(2, $errors);
    }

    /** @test */
    public function getAverage()
    {
        $recipe = $this->getEntity();
        $user = static::getContainer()->get("doctrine.orm.entity_manager")->find(User::class,1);

        for ($i=0; $i < 5; $i++) 
        { 
            $rating = new Rating();
            $rating->setRating(2.0)
                    ->setUser($user)
                    ->setRecipe($recipe)
            ;

            $recipe->addRating($rating);
        }

        $this->assertTrue(2.0 === $recipe->getAverage());
    }

    
}
