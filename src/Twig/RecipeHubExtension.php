<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RecipeHubExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [$this, 'formatTimeAgo']),
            new TwigFilter('cooking_time_format', [$this, 'formatCookingTime']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('difficulty_stars', [$this, 'getDifficultyStars']),
        ];
    }

    public function formatTimeAgo(?\DateTimeInterface $date): string
    {
        if (!$date) {
            return '';
        }

        $now = new \DateTimeImmutable();
        $interval = $date->diff($now);

        $minutes = $interval->i + ($interval->h * 60) + ($interval->d * 1440) + ($interval->m * 43200) + ($interval->y * 518400);

        if ($minutes < 1) {
            return "À l'instant";
        }
        if ($minutes < 60) {
            return "il y a $minutes min";
        }
        if ($minutes < 1440) {
            $hours = floor($minutes / 60);
            return "il y a $hours h";
        }
        if ($minutes < 43200) {
            $days = floor($minutes / 1440);
            return "il y a $days jour" . ($days > 1 ? 's' : '');
        }
        if ($minutes < 518400) {
            $months = floor($minutes / 43200);
            return "il y a $months mois";
        }

        $years = floor($minutes / 518400);
        return "il y a $years an" . ($years > 1 ? 's' : '');
    }

    public function formatCookingTime(?int $minutes): string
    {
        if ($minutes === null || $minutes <= 0) {
            return '—';
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours === 0) {
            return "{$minutes}min";
        }
        if ($mins === 0) {
            return "{$hours}h";
        }
        return "{$hours}h{$mins}";
    }

    public function getDifficultyStars(?string $difficulte): string
    {
        return match (strtolower((string) $difficulte)) {
            'facile' => '⭐',
            'moyen' => '⭐⭐',
            'difficile' => '⭐⭐⭐',
            default => '—',
        };
    }
}
