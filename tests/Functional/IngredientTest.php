<?php

namespace App\Tests\Functional;

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

        //* Go to create ingredient page (ingredient.new))
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
}
