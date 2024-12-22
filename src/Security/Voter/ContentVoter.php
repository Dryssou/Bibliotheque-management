<?php
// src/Security/Voter/ContentVoter.php
namespace App\Security\Voter;

use App\Entity\Book;
use App\Entity\Discussion;
use App\Entity\Comment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ContentVoter extends Voter
{
    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['edit', 'delete'])
            && ($subject instanceof Book
                || $subject instanceof Discussion
                || $subject instanceof Comment);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_BANNED')) {
            return false;
        }

        switch ($attribute) {
            case 'edit':
            case 'delete':
                if ($subject instanceof Discussion || $subject instanceof Comment) {
                    return $subject->getUser() === $user;
                }
                break;
        }

        return false;
    }
}