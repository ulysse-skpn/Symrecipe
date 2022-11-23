<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        
        if( $this->getUser() )
        {
            $contact->setFullName($this->getUser()->getFullName()) //? Ne pas tenir compte du message d'erreur
                    ->setEmail($this->getUser()->getEmail()) //? Ne pas tenir compte du message d'erreur
            ;
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) 
        { 

            $manager->persist($contact);
            $manager->flush();

            // $email = (new Email())
            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('you@example.com')
            ->subject($contact->getSubject())
            ->htmlTemplate('email/contact.html.twig')   // path of the Twig template to render
            ->context([
                'contact' => $contact
            ])
            ;

            $mailer->send($email);

            $this->addFlash(
                "success",
                "Le formulaire à bien été envoyé"
            );

            return $this->redirectToRoute("home");
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
