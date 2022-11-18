<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    /**
     * Update an User
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/edit/{id}', name: 'user.edit', methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class,$choosenUser);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {
            if( $hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword()) )
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

    /**
     * Modify the user password
     *
     * @param User $choosenUser
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/user/password/{id}', name: 'user.password', methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function editPassword(User $choosenUser, EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $hasher): Response
    {   
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) 
        { 
            if( $hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword']))
            {
                $choosenUser->setUpdatedAt( new \DateTimeImmutable() );
                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $manager->persist($choosenUser);
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
