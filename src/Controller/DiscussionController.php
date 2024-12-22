<?php
namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Comment;
use App\Form\DiscussionType;
use App\Form\CommentType;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/discussion')]
#[IsGranted('ROLE_USER')]
class DiscussionController extends AbstractController
{
    #[Route('/', name: 'app_discussion_index', methods: ['GET'])]
    public function index(DiscussionRepository $discussionRepository): Response
    {
        return $this->render('discussion/index.html.twig', [
            'discussions' => $discussionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_discussion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $discussion = new Discussion();
        $discussion->setUser($this->getUser());
        
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($discussion);
            $entityManager->flush();

            $this->addFlash('success', 'La discussion a été créée avec succès.');
            return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()]);
        }

        return $this->render('discussion/new.html.twig', [
            'discussion' => $discussion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_discussion_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setDiscussion($discussion);

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté.');
            return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()]);
        }

        return $this->render('discussion/show.html.twig', [
            'discussion' => $discussion,
            'commentForm' => $commentForm,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_discussion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $discussion);

        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La discussion a été modifiée avec succès.');
            return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()]);
        }

        return $this->render('discussion/edit.html.twig', [
            'discussion' => $discussion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/resolve', name: 'app_discussion_resolve', methods: ['POST'])]
    public function resolve(Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $discussion);

        $discussion->setIsResolved(!$discussion->isResolved());
        $entityManager->flush();

        $status = $discussion->isResolved() ? 'résolue' : 'non résolue';
        $this->addFlash('success', "La discussion est maintenant $status.");

        return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()]);
    }

    #[Route('/{id}', name: 'app_discussion_delete', methods: ['POST'])]
    public function delete(Request $request, Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $discussion);

        if ($this->isCsrfTokenValid('delete'.$discussion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($discussion);
            $entityManager->flush();
            
            $this->addFlash('success', 'La discussion a été supprimée.');
        }

        return $this->redirectToRoute('app_discussion_index');
    }
}