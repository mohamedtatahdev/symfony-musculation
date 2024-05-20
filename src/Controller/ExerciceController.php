<?php

namespace App\Controller;

use App\Entity\Vote;
use App\Entity\Comment;
use App\Entity\Exercice;
use App\Form\CommentType;
use App\Form\ExerciceType;
use App\Repository\VoteRepository;
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

      #[Route('/exercice/rating/{id}/{score}', name: 'exercice_rating')]
      public function ratingQuestion(Request $request, Exercice $exercice, int $score, EntityManagerInterface $em, VoteRepository $voteRepo)
      {
        $user = $this->getUser();
        if ($user !== $exercice->getUser()) {
          $vote = $voteRepo->findOneBy([
            'user' => $user,
            'exercice' => $exercice
          ]);
          if ($vote) {
            if (($vote->getIsLiked() && $score > 0) || (!$vote->getIsLiked() && $score < 0)) {
              $em->remove($vote);
              $exercice->setRating($exercice->getRating() + ($score > 0 ? -1 : 1));
            } else {
              $vote->setIsLiked(!$vote->getIsLiked());
              $exercice->setRating($exercice->getRating() + ($score > 0 ? 2 : -2));
            }
          } else {
            $vote = new Vote();
            $vote->setUser($user);
            $vote->setExercice($exercice);
            $vote->setIsLiked($score > 0 ? true : false);
            $exercice->setRating($exercice->getRating() + $score);
            $em->persist($vote);
          }
          $em->flush();
        }
        $referer = $request->server->get('HTTP_REFERER');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('home');
      }

}



