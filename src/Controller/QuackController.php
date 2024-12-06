<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Form\CommentType;
use App\Form\QuackType;
use App\Form\SearchType;
use App\Repository\QuackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quack')]
final class QuackController extends AbstractController
{
    #[Route(name: 'app_quack_index', methods: ['GET', 'POST'])]
    public function index(Request $request, QuackRepository $quackRepository): Response
    {
        // Créer le formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Quacks visibles par défaut
        $quacks = $quackRepository->findVisibleQuacks();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $keyword = $data['query'];

            // Rechercher uniquement parmi les quacks visibles
            $quacks = $quackRepository->searchVisibleQuacks($keyword);
        }

        return $this->render('quack/index.html.twig', [
            'quacks' => $quacks,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_quack_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quack = new Quack();
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour créer un quack.');
            }
            $quack->setAuthor($user);
            $quack->setCreatedAt(new \DateTime());

            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('uploads_directory'), // Configure ce paramètre dans services.yaml
                    $newFilename
                );
                $quack->setPhoto($newFilename);
            }

            $entityManager->persist($quack);
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_quack_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        // Création d'un nouveau commentaire (quack enfant)
        $comment = new Quack();
        $comment->setParent($quack); // Définit le quack parent
        $comment->setAuthor($this->getUser()); // Définit l'utilisateur connecté comme auteur
        $comment->setIsComment(true);

        // Formulaire pour le commentaire
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_show', ['id' => $quack->getId()]);
        }

        // Rendu de la page avec le formulaire de commentaire
        return $this->render('quack/show.html.twig', [
            'quack' => $quack, // Quack principal
            'form' => $form->createView(), // Formulaire pour le commentaire
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_quack_delete', methods: ['POST'])]
    public function delete(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Si c'est un commentaire (il a un parent)
        if ($quack->getParent()) {
            $canDelete = $quack->getAuthor() === $user || $quack->getParent()->getAuthor() === $user;

            if (!$canDelete) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire.');
            }

            if ($this->isCsrfTokenValid('delete'.$quack->getId(), $request->request->get('_token'))) {
                $entityManager->remove($quack);
                $entityManager->flush();

                return $this->redirectToRoute('app_quack_show', ['id' => $quack->getParent()->getId()]);
            }
        } else {
            // Si c'est un post principal
            $canDelete = $quack->getAuthor() === $user;

            if (!$canDelete) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce post.');
            }

            if ($this->isCsrfTokenValid('delete'.$quack->getId(), $request->request->get('_token'))) {
                $entityManager->remove($quack);
                $entityManager->flush();

                return $this->redirectToRoute('app_quack_index');
            }
        }

        return $this->redirectToRoute('app_quack_index');
    }


    #[Route('/{id}/moderate', name: 'app_quack_moderate', methods: ['POST'])]
    public function moderate(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur peut modérer
        $this->denyAccessUnlessGranted('QUACK_MODERATE', $quack);

        $quack->setModerated(true); // Masque le quack
        $entityManager->flush();

        $this->addFlash('success', 'Le quack a été masqué avec succès.');

        return $this->redirectToRoute('app_quack_index');
    }
}
