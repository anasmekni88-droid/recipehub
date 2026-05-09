<?php

namespace App\Security\Voter;

use App\Entity\Recette;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RecetteVoter extends Voter
{
    public const CREATE = 'create';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const PUBLISH = 'publish';
    public const UNPUBLISH = 'unpublish';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE, self::PUBLISH, self::UNPUBLISH])) {
            return $subject instanceof Recette || $subject === null;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $recette = $subject;
        $isOwner = $recette instanceof Recette && $recette->getAuteur() === $user;
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);
        $isCuisinier = in_array('ROLE_CUISINIER', $user->getRoles(), true);

        return match ($attribute) {
            self::CREATE => $isAdmin || $isCuisinier,
            self::VIEW => $recette === null || $recette->isPubliee() || $isOwner,
            self::EDIT, self::DELETE, self::PUBLISH, self::UNPUBLISH => $isOwner,
            default => false,
        };
    }
}
