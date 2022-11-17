<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/update/{id}', name: 'user.edit')]
    public function edit(User $user, Request $request): Response
    {
        if( !$this->getUser() || $this->getUser() !== $user ) return $this->redirectToRoute('security.login');

        $form = $this->createForm(UserType::class,$user);

        $form->handleRequest($request);

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
