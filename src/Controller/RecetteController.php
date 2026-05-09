<?php

namespace App\Controller;

use App\Entity\CategorieRecette;
use App\Entity\Recette;
use App\Entity\TagRecette;
use App\Form\RecetteType;
use App\Repository\CategorieRecetteRepository;
use App\Repository\RecetteRepository;
use App\Repository\TagRecetteRepository;
use App\Security\Voter\RecetteVoter;
use App\Service\FileUploader;
use App\Service\RecetteAnalyser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recette')]
final class RecetteController extends AbstractController
{
    public function __construct(
        private RecetteAnalyser $analyser,
    ) {}

    #[Route(name: 'app_recette_index', methods: ['GET'])]
    public function index(
        Request $request,
        RecetteRepository $recetteRepository,
        CategorieRecetteRepository $categorieRecetteRepository,
        TagRecetteRepository $tagRecetteRepository,
    ): Response {
        $titre = $request->query->get('titre');
        $categorieId = $request->query->get('categorie');
        $difficulte = $request->query->get('difficulte');
        $tagId = $request->query->get('tag');

        $cat = $categorieId ? $categorieRecetteRepository->find($categorieId) : null;
        $tag = $tagId ? $tagRecetteRepository->find($tagId) : null;

        return $this->render('recette/index.html.twig', [
            'recettes' => $recetteRepository->findByFilters($titre, $cat, $difficulte, $tag),
            'categories' => $categorieRecetteRepository->findAll(),
            'tags' => $tagRecetteRepository->findAll(),
        ]);
    }

    #[Route('/mes-recettes', name: 'app_recette_mes_recettes', methods: ['GET'])]
    #[IsGranted('ROLE_CUISINIER')]
    public function mesRecettes(
        Request $request,
        RecetteRepository $recetteRepository,
        CategorieRecetteRepository $categorieRecetteRepository,
        TagRecetteRepository $tagRecetteRepository,
    ): Response {
        $titre = $request->query->get('titre');
        $categorieId = $request->query->get('categorie');
        $difficulte = $request->query->get('difficulte');
        $tagId = $request->query->get('tag');

        $cat = $categorieId ? $categorieRecetteRepository->find($categorieId) : null;
        $tag = $tagId ? $tagRecetteRepository->find($tagId) : null;

        $qb = $recetteRepository->createQueryBuilder('r')
            ->andWhere('r.auteur = :auteur')->setParameter('auteur', $this->getUser())
            ->andWhere('r.publiee = true');

        if ($titre) {
            $qb->andWhere('r.titre LIKE :titre')->setParameter('titre', "%$titre%");
        }
        if ($cat) {
            $qb->andWhere('r.categorie = :cat')->setParameter('cat', $cat);
        }
        if ($difficulte) {
            $qb->andWhere('r.difficulte = :diff')->setParameter('diff', $difficulte);
        }
        if ($tag) {
            $qb->innerJoin('r.tags', 't')->andWhere('t = :tag')->setParameter('tag', $tag);
        }

        return $this->render('recette/index.html.twig', [
            'recettes' => $qb->orderBy('r.dateCreation', 'DESC')->getQuery()->getResult(),
            'categories' => $categorieRecetteRepository->findAll(),
            'tags' => $tagRecetteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recette_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CUISINIER')]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageName = $fileUploader->upload($imageFile);
                $recette->setImageName($imageName);
            }

            $recette->setAuteur($this->getUser());
            $entityManager->persist($recette);
            $entityManager->flush();

            if ($recette->isPubliee()) {
                return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_recette_drafts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recette_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        $this->denyAccessUnlessGranted(RecetteVoter::VIEW, $recette);

        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    #[Route('/brouillons', name: 'app_recette_drafts', methods: ['GET'])]
    #[IsGranted('ROLE_CUISINIER')]
    public function drafts(RecetteRepository $recetteRepository): Response
    {
        $recettes = $recetteRepository->findBy(['publiee' => false, 'auteur' => $this->getUser()]);

        return $this->render('recette/drafts.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/{id}/publier', name: 'app_recette_publish', methods: ['POST'])]
    public function publish(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(RecetteVoter::PUBLISH, $recette);

        if ($this->isCsrfTokenValid('publish' . $recette->getId(), $request->request->get('_token'))) {
            $recette->setPubliee(true);
            $entityManager->flush();
            $this->addFlash('success', 'Recette publiée avec succès.');
        }

        return $this->redirectToRoute('app_recette_drafts', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/depublier', name: 'app_recette_unpublish', methods: ['POST'])]
    public function unpublish(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(RecetteVoter::UNPUBLISH, $recette);

        if ($this->isCsrfTokenValid('unpublish' . $recette->getId(), $request->request->get('_token'))) {
            $recette->setPubliee(false);
            $entityManager->flush();
            $this->addFlash('success', 'Recette déplacée dans les brouillons.');
        }

        return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted(RecetteVoter::EDIT, $recette);

        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $fileUploader->remove($recette->getImageName());
                $imageName = $fileUploader->upload($imageFile);
                $recette->setImageName($imageName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted(RecetteVoter::DELETE, $recette);

        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->getPayload()->getString('_token'))) {
            $fileUploader->remove($recette->getImageName());
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
    }
}
