<?php

namespace App\Controller;

use App\Entity\Muscle;
use App\Form\MuscleType;
use App\Repository\MuscleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/muscle')]
class MuscleController extends AbstractController
{
    #[Route('/', name: 'app_muscle_index', methods: ['GET'])]
    public function index(MuscleRepository $muscleRepository): Response
    {
        return $this->render('muscle/index.html.twig', [
            'muscles' => $muscleRepository->findAll(),
        ]);
    }


#[Route('/categorie/{slug}', name: 'category')]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBySlug($slug);//recupere les cat en fonction du slug

        if(!$category){
            return $this->redirectToRoute('home');//redirige la route si la cat n'existe pas
        }

        

        return $this->render('muscle/category.html.twig', [
            'category' => $category,
        ]);
    }

}
