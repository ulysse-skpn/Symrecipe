<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * Display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredients', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findBy(["user" => $this->getUser()]), /* query NOT result */ //?
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        // $ingredients = [];

        return $this->render('ingredient/index.html.twig', [
            'ingredientList' => $ingredients,
        ]);
    }

    /**
     * Add a new ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/new', name: 'ingredient.new', methods:['GET','POST'])]
    public function new_ingredient(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {
            $ingredient = $form->getData();

            $ingredient->setUser( $this->getUser() );
            
            $manager->persist($ingredient);
            $manager->flush();
            
            $this->addFlash(
                'primary',
                'Ingrédient enregistré avec succès'
            );

            return $this->redirectToRoute('ingredients');
        }

        return $this->render('ingredient/new.html.twig',
        [
            'form' => $form->createView() 
        ]);
    }

    #[Route('/ingredient/edit/{id}', name: 'ingredient.edit', methods:['GET','POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Ingrédient modifié avec succès'
            );

            return $this->redirectToRoute('ingredients');
        }

        return $this->render('ingredient/edit.html.twig',
        [
            'form' => $form->createView(),
            'ingredient' => $ingredient
        ]);
    }

    #[Route('/ingredient/delete/{id}', name: 'ingredient.delete', methods:['GET','DELETE'])]
    public function FunctionName(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {   
        if( !$ingredient )  
        {
            $this->addFlash(
                'warning',
                "L'ingrédient n'existe pas"
            );
            return $this->redirectToRoute('ingredients');
        }
    
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Ingrédient supprimé avec succès'
        );

        return $this->redirectToRoute('ingredients');
    }
}
