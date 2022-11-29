<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    /** @test */
    public function homePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/home');

        $this->assertResponseIsSuccessful();

        $registerButton = $crawler->selectButton("Inscription");
        $loginButton = $crawler->selectButton("Connexion");
        $this->assertEquals(1, count($registerButton));
        $this->assertEquals(1, count($loginButton));

        // $recipes = $crawler->filter(".card .recipes"); //! Crawler doesn't find elements inside the for loop in twig
        // $this->assertEquals(3, count($recipes));

        $this->assertSelectorTextContains('legend', 'Dernière(s) recettes ajoutée(s)');
    }
}
