<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    /** @test */
    public function contactForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('legend', 'Formulaire de contact');

        // Récupérer le formulaire
        $submitButton = $crawler->selectButton("Envoyer");
        $form = $submitButton->form();

        $form["contact[fullName]"] = "Jean François";
        $form["contact[email]"] = "jf@symrecipe.net";
        $form["contact[subject]"] = "Sujet de la demande de contact";
        $form["contact[message]"] = "Message de la demande de contact";

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier le statut HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Vérifier l'envoi du mail (queued)
        $this->assertQueuedEmailCount(1);

        $client->followRedirect();
        // Vérifier la présence du message de succès

        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            'Le formulaire à bien été envoyé'
        );

        // OR

        $this->assertResponseIsSuccessful();
    }
}
