<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/user/update/{id}', name: 'user.edit', methods:['GET','POST'])]
    /**
     * Update an User
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if( !$this->getUser() || $this->getUser() !== $user ) return $this->redirectToRoute('security.login');

        $form = $this->createForm(UserType::class,$user);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {
            if( $hasher->isPasswordValid($user, $form->getData()->getPlainPassword()) )
            {
                $user = $form->getData();
    
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Modification(s) enregistrée(s)'
                );
    
                return $this->redirectToRoute('security.login');
            }
            else
            {
                $this->addFlash(
                    'warning',
                    'Le mot de passe est renseigné est incorrect'
                );
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/password/{id}', name: 'user.password', methods:['GET','POST'])]
    /**
     * Modify the user password
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function editPassword(User $user, EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        if( !$this->getUser() || $this->getUser() !== $user ) return $this->redirectToRoute("security.login");
        
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) 
        { 
            if( $hasher->isPasswordValid($user, $form->getData()['plainPassword']))
            {
                $user->setUpdatedAt( new \DateTimeImmutable() );
                $user->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passe à bien été modifié'
                );

                return $this->redirectToRoute('security.logout');
            }
            else
            {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
        }

        return $this->render("user/edit_password.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
