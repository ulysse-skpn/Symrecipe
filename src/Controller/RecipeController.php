<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\Recipe;
use App\Form\RatingType;
use App\Form\RecipeType;
use App\Repository\RatingRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
     * Show details user recipe by id
     *
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recipe/show/{id}', name: 'recipe.show', methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER')")]
    public function show(Recipe $recipe, Request $request, RatingRepository $repository, EntityManagerInterface $manager): Response
    {
        $rating  = new Rating();
        $form = $this->createForm(RatingType::class, $rating);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $rating->setUser( $this->getUser() )
                    ->setRecipe($recipe);

            $existingRating = $repository->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe
            ]);

            if( !$existingRating )
            {
                $manager->persist($rating);
            }
            else    //? Pas sûr de l'utilité pour le moment
            {
                $existingRating->setRating(
                    $form->getData()->getRating()
                );
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre notre à bien été pris en compte'
            );

            return $this->redirectToRoute("recipe.show", ['id' => $recipe->getId() ]);
        }

        return $this->render("recipe/show.html.twig", [
            "recipe" => $recipe,
            "form" => $form->createView()
        ]);
    }



    /**
     * Show details public recipe by id
     *
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recipe/show/public/{id}', name: 'recipe.show.public', methods:['GET','POST'])]
    public function show_public(Recipe $recipe): Response
    {
        return $this->render("recipe/show.html.twig", [
            "recipe" => $recipe
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
    #[Route('/recipe/edit/{id}', name: 'recipe.edit', methods:['GET','POST'])]
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

            return $this->redirectToRoute('recipes');
        }

        
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }


    /**
     * Delete a recipe
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recipe/delete/{id}', name: 'recipe.delete', methods:['GET','DELETE'])]
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

    #[Route('/recipe/public/{nbRecipes}', name: 'recipe.public', methods:['GET'], defaults:["nbRecipes" => 20])]
    public function index_public(PaginatorInterface $paginator, RecipeRepository $repository, Request $request, CacheInterface $cacheInterface, int $nbRecipes): Response
    {
        //! Using cache doesn't return the recipe user
        // $data = $cacheInterface->get('recipes', function(ItemInterface $itemInterface) use($repository,$nbRecipes){
        //     $itemInterface->expiresAfter(60 * 60 * 24);
        //     return $repository->findPublicRecipe($nbRecipes);
        // });

        // $recipes = $paginator->paginate(
        //     $data,
        //     $request->query->getInt('page',1),
        //     10
        // );

        $recipes = $paginator->paginate(
            $repository->findPublicRecipe($nbRecipes),
            $request->query->getInt('page',1),
            10
        );

        return $this->render("recipe/index_public.html.twig",[
            "recipes" => $recipes
        ]);
    }

    #[Route('/recipe/toggle_public_recipe/{id}', name:"recipe.toggle.public", methods:['GET','POST'])]
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    public function toggle_public_recipe(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        if(!$recipe)
        {
            $this->addFlash(
                'warning',
                "Un problème avec la recette est survenu"
            );
            return $this->redirectToRoute('recipes');
        }

        $recipe->setIsPublic( !$recipe->getIsPublic() );

        if( $recipe->getIsPublic() === true ) $message = "Recette partagée avec succès";
        else $message = "La recette n'est plus publique";

        $manager->persist($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            $message
        );

        return $this->redirectToRoute('recipes');
    }
}
