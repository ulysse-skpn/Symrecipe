<?php

namespace App\Tests\Functional;

use App\Entity\Ingredient;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    /** @test */ 
    public function testIfCreateIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        // UrlGenerator
        // EntityManager
        // Get current user
        // Go to create ingredient page (ingredient.new))
        // Manage Form
        // Redirection
        // Manage Alert box and route

        //* UrlGenerator
        $urlGenerator = $client->getContainer()->get("router");

        //* EntityManager
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        //* Get current user
        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        //* Go to create ingredient page (ingredient.new)
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.new'));

        //* Manage Form
        $form = $crawler->filter('form[name=ingredient]')->form([
            "ingredient[name]" => "Ingrédient Test",
            "ingredient[price]" => floatval(33),
        ]);

        $client->submit($form);

        //* Redirection
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        //* Manage Alert box and route
        $this->assertSelectorTextContains("div.alert-primary","Ingrédient enregistré avec succès");

        $this->assertRouteSame("ingredients");
    }

    /** @test */
    public function testIfGetAllIngredientsWorks(): void
    {
        $client = static::createClient();

        //* UrlGenerator
        $urlGenerator = $client->getContainer()->get("router");

        //* EntityManager
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        //* Get current user
        $user = $entityManager->find(User::class, 2);

        $client->loginUser($user);
        
        //* Go to list all ingredients page (ingredients)
        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredients'));

        $this->assertSelectorTextContains("h2","Listes des ingrédients");

        $this->assertRouteSame("ingredients");
    }

    /** @test */
    public function testIfUpdateIngrdientsWorks(): void
    {
        $client = static::createClient();

        //* UrlGenerator
        $urlGenerator = $client->getContainer()->get("router");

        //* EntityManager
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        //* Get current user
        $user = $entityManager->find(User::class, 2);

        $client->loginUser($user);

        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            "user" => $user
        ]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.edit', ["id" => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("form[name='ingredient']")->form([
                "ingredient[name]" => "modif Ingrédient Test",
                "ingredient[price]" => floatval(99.9),
            ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains("div.alert-success","Ingrédient modifié avec succès");
    }

    /** @test */
    public function testIfDeleteIngrdientsWorks(): void
    {
        $client = static::createClient();

        //* UrlGenerator
        $urlGenerator = $client->getContainer()->get("router");

        //* EntityManager
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        //* Get current user
        $user = $entityManager->find(User::class, 2);

        $client->loginUser($user);

        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            "user" => $user
        ]);

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.delete', ["id" => $ingredient->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains("div.alert-success","Ingrédient supprimé avec succès");
    }
}
