<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Discussion;
use App\Repository\UserRepository;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        BookRepository $bookRepository,
        AuthorRepository $authorRepository,
        DiscussionRepository $discussionRepository
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'users' => $userRepository->count([]),
                'books' => $bookRepository->count([]),
                'authors' => $authorRepository->count([]),
                'discussions' => $discussionRepository->count([]),
            ],
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/books', name: 'app_admin_books')]
    public function books(BookRepository $bookRepository): Response
    {
        return $this->render('admin/books.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/authors', name: 'app_admin_authors')]
    public function authors(AuthorRepository $authorRepository): Response
    {
        return $this->render('admin/authors.html.twig', [
            'authors' => $authorRepository->findAll(),
        ]);
    }

    #[Route('/discussions', name: 'app_admin_discussions')]
    public function discussions(DiscussionRepository $discussionRepository): Response
    {
        return $this->render('admin/discussions.html.twig', [
            'discussions' => $discussionRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}/toggle-role/{role}', name: 'app_admin_toggle_role', methods: ['POST'])]
    public function toggleRole(
        User $user, 
        string $role, 
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid('toggle-role'.$user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        // Empêcher la modification du rôle ROLE_USER
        if ($role === 'ROLE_USER') {
            $this->addFlash('error', 'Le rôle ROLE_USER ne peut pas être modifié.');
            return $this->redirectToRoute('app_admin_users');
        }

        // Empêcher l'admin de modifier son propre rôle
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier vos propres rôles.');
            return $this->redirectToRoute('app_admin_users');
        }

        $roles = $user->getRoles();
        if (in_array($role, $roles)) {
            $roles = array_diff($roles, [$role]);
            $message = 'Rôle retiré avec succès.';
        } else {
            $roles[] = $role;
            $message = 'Rôle ajouté avec succès.';
        }
        
        $user->setRoles(array_values(array_unique($roles)));
        $this->entityManager->flush();

        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/user/{id}/ban', name: 'app_admin_ban_user', methods: ['POST'])]
    public function banUser(
        User $user, 
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid('ban-user'.$user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        // Empêcher l'admin de se bannir lui-même
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous bannir vous-même.');
            return $this->redirectToRoute('app_admin_users');
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_BANNED', $roles)) {
            $roles = array_diff($roles, ['ROLE_BANNED']);
            $message = 'Utilisateur débanni avec succès.';
        } else {
            $roles[] = 'ROLE_BANNED';
            $message = 'Utilisateur banni avec succès.';
        }
        
        $user->setRoles(array_values(array_unique($roles)));
        $this->entityManager->flush();

        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/book/{id}/delete', name: 'app_admin_book_delete', methods: ['POST'])]
    public function deleteBook(
        Book $book, 
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        $this->addFlash('success', 'Livre supprimé avec succès.');
        return $this->redirectToRoute('app_admin_books');
    }

    #[Route('/author/{id}/delete', name: 'app_admin_author_delete', methods: ['POST'])]
    public function deleteAuthor(
        Author $author, 
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $this->entityManager->remove($author);
        $this->entityManager->flush();

        $this->addFlash('success', 'Auteur supprimé avec succès.');
        return $this->redirectToRoute('app_admin_authors');
    }

    #[Route('/discussion/{id}/delete', name: 'app_admin_discussion_delete', methods: ['POST'])]
    public function deleteDiscussion(
        Discussion $discussion, 
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid('delete'.$discussion->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $this->entityManager->remove($discussion);
        $this->entityManager->flush();

        $this->addFlash('success', 'Discussion supprimée avec succès.');
        return $this->redirectToRoute('app_admin_discussions');
    }
}