<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * Get all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe', name: 'recipes', methods:['GET'])]
    #[IsGranted("ROLE_USER")]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(["user" => $this->getUser()]),
            $request->query->getInt('page',1),
            10
        );
        // $recipes = [];

        // dd($recipes);
        return $this->render('recipe/index.html.twig', [
            'recipeList' => $recipes,
        ]);
    }

    /**
     * Add a new recipe
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe/new', name: 'recipe.new', methods:['GET','POST'])]
    #[IsGranted("ROLE_USER")]
    public function new(EntityManagerInterface $manager, Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest(($request));

        if( $form->isSubmitted() && $form->isValid() )
        {
            $recipe = $form->getData();

            $recipe->setUser( $this->getUser() );

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Recette enregistrée avec succès'
            );

            return $this->redirectToRoute('recipes');
        }

        
        return $this->render('recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Update a recipe
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe/{id}', name: 'recipe.edit', methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    public function edit(Recipe $recipe, EntityManagerInterface $manager, Request $request): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest(($request));

        if( $form->isSubmitted() && $form->isValid() )
        {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Recette modifiée avec succès'
            );
        }

        return $this->redirectToRoute('recipes');
        
        return $this->render('recipe/index.html.twig', [
            'recipe' => $recipe,
        ]);
    }


    /**
     * Delete a recipe
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recipe/{id}', name: 'recipe.delete', methods:['GET','DELETE'])]
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    public function delete(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        if(!$recipe)
        {
            $this->addFlash(
                'warning',
                "La recette n'existe pas"
            );
            return $this->redirectToRoute('recipes');
        }

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Recette supprimée avec succès'
        );

        return $this->redirectToRoute('recipes');
    }
}
