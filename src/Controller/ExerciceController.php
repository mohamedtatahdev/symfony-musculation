<?php

namespace App\Controller;

use App\Entity\Vote;
use App\Entity\Comment;
use App\Entity\Exercice;
use App\Form\CommentType;
use App\Form\ExerciceType;
use App\Repository\VoteRepository;
use App\Repository\CommentRepository;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/exercices')]
class ExerciceController extends AbstractController
{
    #[Route('/', name: 'exercice', methods: ['GET'])]
    public function index(ExerciceRepository $exerciceRepository,PaginatorInterface $paginatorInterface, Request $request): Response
    {
      $page = $request->query->getInt('page', 1);

      if (!$this->isGranted('IS_AUTHENTICATED_FULLY') && $page > 1) {
        // Rediriger vers la page de connexion
        return $this->redirectToRoute('login');
    }
        $data = $exerciceRepository->findAll();
        $exercices = $paginatorInterface->paginate(
          $data,
          $page,
          5
        );
        return $this->render('exercice/index.html.twig', [
            'exercices' => $exercices,
        ]);
    }

    #[Route('/{slug}/{id?}', name: 'exercice_show')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function category($slug, ?int $id = null, ExerciceRepository $exerciceRepository,Request $request, EntityManagerInterface $em,CommentRepository $commentRepository): Response
    {
        $exercice = $exerciceRepository->findOneBySlug($slug);//recupere les cat en fonction du slug

        if(!$exercice){
            return $this->redirectToRoute('home');//redirige la route si la cat n'existe pas
        }

        $user = $this->getUser();
        $comment = $id ? $commentRepository->find($id) : new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
          if (!$id) {
              $comment->setCreatedAt(new \DateTimeImmutable());
              $comment->setExercice($exercice);
              $comment->setUser($user);
              $em->persist($comment);
          }
          $em->flush();
  
          return $this->redirectToRoute('exercice_show', ['slug' => $slug]);

        }
        
        
    

        return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
            'form' => $commentForm->createView(),
            'is_edit' => $id !== null,


        ]);
    }

    #[Route('/comment/delete/{id}', name: 'comment_delete')]
public function deleteComment(int $id, CommentRepository $commentRepository, Request $request, EntityManagerInterface $em): Response
{
    $comment = $commentRepository->find($id);

    if (!$comment) {
        throw $this->createNotFoundException('Le commentaire n\'existe pas');
    }

    $user = $this->getUser();

    // Vérifiez si l'utilisateur connecté est bien l'auteur du commentaire
    if ($comment->getUser() !== $user) {
        throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer ce commentaire');
    }

    $exerciceSlug = $comment->getExercice()->getSlug();

    $em->remove($comment);
    $em->flush();

    return $this->redirectToRoute('exercice_show', ['slug' => $exerciceSlug]);
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



