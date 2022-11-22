<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home/{nbRecipes}', name: 'home', methods:['GET'], defaults:["nbRecipes" => 3])]
    public function index(RecipeRepository $repository, int $nbRecipes): Response
    {
        return $this->render('home/index.html.twig', [
            'recipes' => $repository->findPublicRecipe($nbRecipes),
        ]);
    }

    #[Route('/', name: 'home.root', methods:['GET'])]
    public function _redirect(): Response
    {
        return $this->redirectToRoute("home");
    }
}
