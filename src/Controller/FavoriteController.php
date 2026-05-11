<?php

namespace App\Controller;

use App\Repository\RecetteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favori')]
final class FavoriteController extends AbstractController
{
    #[Route('/ajouter/{recetteId}', name: 'app_favorite_add', methods: ['POST'])]
    public function add(Request $request, RecetteRepository $recetteRepository): Response
    {
        $recetteId = (int) $request->attributes->get('recetteId');
        $recette = $recetteRepository->find($recetteId);
        if (!$recette) {
            throw $this->createNotFoundException('Recette not found');
        }

        $session = $request->getSession();
        $favorites = $session->get('favorites', []);

        if (!in_array($recetteId, $favorites, true)) {
            $favorites[] = $recetteId;
            $session->set('favorites', $favorites);
            $this->addFlash('success', 'Recette ajoutée aux favoris');
        } else {
            $this->addFlash('info', 'Cette recette est déjà dans vos favoris');
        }

        return $this->redirectToRoute('app_recette_show', ['id' => $recetteId]);
    }

    #[Route('/retirer/{recetteId}', name: 'app_favorite_remove', methods: ['POST'])]
    public function remove(Request $request): Response
    {
        $recetteId = (int) $request->attributes->get('recetteId');
        $session = $request->getSession();
        $favorites = $session->get('favorites', []);

        $favorites = array_filter($favorites, fn(int $fid) => $fid !== $recetteId);
        $session->set('favorites', array_values($favorites));

        $this->addFlash('success', 'Recette retirée des favoris');

        $referer = $request->headers->get('referer', $this->generateUrl('app_favorite_index'));
        return $this->redirect($referer);
    }

    #[Route(name: 'app_favorite_index', methods: ['GET'])]
    public function index(Request $request, RecetteRepository $recetteRepository, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $favoriteIds = $session->get('favorites', []);

        $recettes = [];
        if (!empty($favoriteIds)) {
            $recettes = $recetteRepository->findBy(['id' => $favoriteIds]);
            $order = array_flip($favoriteIds);
            usort($recettes, fn($a, $b) => ($order[$a->getId()] ?? 0) <=> ($order[$b->getId()] ?? 0));
        }

        return $this->render('favorite/index.html.twig', [
            'recettes' => $paginator->paginate($recettes, $request->query->getInt('page', 1), 9),
        ]);
    }
}
