<?php

namespace App\Controller;

use App\Classe\Favory;
use App\Repository\ExerciceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FavoryController extends AbstractController
{
    #[Route('/mes-favoris', name: 'favory')]
    public function index(Favory $favory): Response
    {
        return $this->render('favory/index.html.twig', [
            'favory' => $favory->getFavory()
        ]);
    }

    #[Route('/favory/add/{id}', name: 'add_favory')]
    public function add($id, Favory $favory, ExerciceRepository $exerciceRepository): Response
    {
        $exercice = $exerciceRepository->findOneById($id);
        $favory->add($exercice);


        dd('produit ajouter au panier');
    }

    #[Route('/favory/delete/{id}', name: 'delete_favory')]
    public function delete($id,  Favory $favory ): Response
    {
        $favory->delete($id);

        return $this->redirectToRoute('favory');
    }
}
