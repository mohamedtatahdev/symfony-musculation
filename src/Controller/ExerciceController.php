<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Exercice;
use App\Form\CommentType;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/exercices')]
class ExerciceController extends AbstractController
{
    #[Route('/', name: 'exercice', methods: ['GET'])]
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('exercice/index.html.twig', [
            'exercices' => $exerciceRepository->findAll(),
        ]);
    }

    #[Route('/{slug}', name: 'exercice_show')]
    public function category($slug, ExerciceRepository $exerciceRepository,Request $request, EntityManagerInterface $em): Response
    {
        $exercice = $exerciceRepository->findOneBySlug($slug);//recupere les cat en fonction du slug

        if(!$exercice){
            return $this->redirectToRoute('home');//redirige la route si la cat n'existe pas
        }

        $user = $this->getUser();

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setExercice($exercice);
            $comment->setUser($user);
            $em->persist($comment);
            $em->flush();
            return $this->redirect($request->getUri());
        }
        
    

        return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
            'form' => $commentForm->createView(),

        ]);
    }
}
