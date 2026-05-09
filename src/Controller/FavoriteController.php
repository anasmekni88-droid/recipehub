<?php

namespace App\Controller;

use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favori')]
final class FavoriteController extends AbstractController
{
    #[Route('/ajouter/{id}', name: 'app_favorite_add', methods: ['POST'])]
    public function add(int $id, Request $request, RecetteRepository $recetteRepository): Response
    {
        $recette = $recetteRepository->find($id);
        if (!$recette) {
            throw $this->createNotFoundException('Recette not found');
        }

        $session = $request->getSession();
        $favorites = $session->get('favorites', []);

        if (!in_array($id, $favorites, true)) {
            $favorites[] = $id;
            $session->set('favorites', $favorites);
            $this->addFlash('success', 'Recette ajoutée aux favoris');
        } else {
            $this->addFlash('info', 'Cette recette est déjà dans vos favoris');
        }

        return $this->redirectToRoute('app_recette_show', ['id' => $id]);
    }

    #[Route('/retirer/{id}', name: 'app_favorite_remove', methods: ['POST'])]
    public function remove(int $id, Request $request): Response
    {
        $session = $request->getSession();
        $favorites = $session->get('favorites', []);

        $favorites = array_filter($favorites, fn(int $fid) => $fid !== $id);
        $session->set('favorites', array_values($favorites));

        $this->addFlash('success', 'Recette retirée des favoris');

        $referer = $request->headers->get('referer', $this->generateUrl('app_favorite_index'));
        return $this->redirect($referer);
    }

    #[Route(name: 'app_favorite_index', methods: ['GET'])]
    public function index(Request $request, RecetteRepository $recetteRepository): Response
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
            'recettes' => $recettes,
        ]);
    }
}
