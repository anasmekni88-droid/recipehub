<?php

namespace App\Twig;

use App\Repository\RecetteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private RecetteRepository $recetteRepository,
        private Security $security,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('draft_count', [$this, 'getDraftCount']),
        ];
    }

    public function getDraftCount(): int
    {
        $user = $this->security->getUser();
        if (!$user) {
            return 0;
        }
        return $this->recetteRepository->count(['publiee' => false, 'auteur' => $user]);
    }
}
