<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class QuackVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    public const DELETE = 'POST_DELETE';
    public const MODERATE = 'QUACK_MODERATE';


    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE,self::MODERATE])
            && $subject instanceof \App\Entity\Quack;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }


        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $subject->getAuthor()->getId() === $user->getId();
                break;

            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                return true;
                break;

            case self::DELETE:
                return $subject->getAuthor()->getId() === $user->getId();

            case self::MODERATE:
                return in_array('ROLE_MODERATOR', $user->getRoles(),true);
        }

        return false;
    }
}
