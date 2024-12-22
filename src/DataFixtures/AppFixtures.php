<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Genre;
use App\Entity\Discussion;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer l'administrateur
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $manager->persist($admin);

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(true);
        $manager->persist($user);

        // Créer les genres
        $genres = [];
        $genreNames = [
            'Roman', 'Science-Fiction', 'Fantasy', 'Policier', 
            'Thriller', 'Romance', 'Histoire', 'Biographie'
        ];
        foreach ($genreNames as $name) {
            $genre = new Genre();
            $genre->setName($name);
            $genre->setDescription("Les livres de genre $name sont caractérisés par...");
            $manager->persist($genre);
            $genres[] = $genre;
        }

        // Créer les auteurs
        $authors = [];
        $authorData = [
            ['Victor', 'Hugo', 'Un des plus grands écrivains français...'],
            ['Albert', 'Camus', 'Écrivain, philosophe, romancier...'],
            ['Émile', 'Zola', 'Écrivain naturaliste français...'],
            ['Simone', 'de Beauvoir', 'Philosophe, romancière...'],
            ['Marcel', 'Proust', 'Écrivain français...']
        ];
        foreach ($authorData as [$firstName, $lastName, $bio]) {
            $author = new Author();
            $author->setName("$firstName $lastName");
            $author->setBiography($bio);
            $manager->persist($author);
            $authors[] = $author;
        }

        // Créer les livres
        $books = [];
        $bookData = [
            ['Les Misérables', 'L\'histoire de Jean Valjean...', 0],
            ['L\'Étranger', 'Aujourd\'hui, maman est morte...', 1],
            ['Germinal', 'La vie des mineurs...', 2],
            ['Le Deuxième Sexe', 'Essai féministe...', 3],
            ['À la recherche du temps perdu', 'Le narrateur se souvient...', 4]
        ];
        foreach ($bookData as [$title, $desc, $authorIndex]) {
            $book = new Book();
            $book->setTitle($title);
            $book->setDescription($desc);
            $book->setAuthor($authors[$authorIndex]);
            $book->setIsbn(substr(md5($title), 0, 13));
            // Ajouter 1-3 genres aléatoires
            $numGenres = rand(1, 3);
            $randomGenres = array_rand($genres, $numGenres);
            if (!is_array($randomGenres)) {
                $randomGenres = [$randomGenres];
            }
            foreach ($randomGenres as $genreIndex) {
                $book->addGenre($genres[$genreIndex]);
            }
            $manager->persist($book);
            $books[] = $book;
        }

        // Créer des discussions
        foreach ($books as $book) {
            $discussion = new Discussion();
            $discussion->setTitle("Discussion sur " . $book->getTitle());
            $discussion->setContent("Que pensez-vous de ce livre ? J'ai particulièrement aimé...");
            $discussion->setUser($user);
            $discussion->setBook($book);
            $manager->persist($discussion);

            // Ajouter des commentaires
            for ($i = 0; $i < rand(2, 5); $i++) {
                $comment = new Comment();
                $comment->setContent("Commentaire #$i sur le livre. Je pense que...");
                $comment->setUser($i % 2 == 0 ? $user : $admin);
                $comment->setDiscussion($discussion);
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}