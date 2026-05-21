<?php

namespace App\Controller;

use App\Entity\Observation;
use App\Form\ObservationType;
use App\Service\FileUploader;
use App\Repository\ObservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/observation')]
final class ObservationController extends AbstractController
{
    public function __construct(
        private FileUploader $fileUploader
    ) {}

    #[Route(name: 'app_observation_index', methods: ['GET'])]
    public function index(
        ObservationRepository $repo,
        PaginatorInterface $paginator,
        Request $request
    ): Response {

        $queryBuilder = $repo->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $queryBuilder
                ->andWhere('o.user = :user')
                ->setParameter('user', $this->getUser());
        }

        $search = $request->query->get('search');

        if ($search) {
            $queryBuilder
                ->andWhere('o.notes LIKE :search OR o.locationName LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $hasImage = $request->query->get('hasImage');

        if ($hasImage) {
            $queryBuilder
                ->andWhere('o.imagePath IS NOT NULL');
        }

        $fromDate = $request->query->get('fromDate');
        $toData = $request->query->get('toDate');

        if ($fromDate && new \DateTime($fromDate) <= new \DateTime()) {
            $queryBuilder
                ->andWhere('o.observedAt >= :fromDate')
                ->setParameter('fromDate', new \DateTime($fromDate));
        }

        if ($toData && new \DateTime($toData) <= new \DateTime()) {
            $queryBuilder
                ->andWhere('o.observedAt <= :toDate')
                ->setParameter('toDate', new \DateTime($toData));
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        // get all parameters en remove, page
        $queryParams = $request->query->all();
        unset($queryParams['page']);
        $hasFilters = count($queryParams) > 0;

        return $this->render('observation/index.html.twig', [
            'pagination' => $pagination,
            'hasFilters' => $hasFilters,
        ]);
    }

    #[Route('/new', name: 'app_observation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $observation->setUser($this->getUser());

            if (!$observation->getObservedAt()) {
                $observation->setObservedAt(new \DateTimeImmutable());
            }

            $imageFile = $form->get('imagePath')->getData();

            if ($imageFile) {
                $newFilename = $this->fileUploader->upload(
                    $imageFile,
                    $this->getParameter('uploads_observations_dir')
                );

                $observation->setImagePath('/uploads/observations/' . $newFilename);
            }

            $em->persist($observation);
            $em->flush();

            $this->addFlash('success', 'Observatie succesvol toegevoegd.');
            return $this->redirectToRoute('app_observation_new');
        }
        return $this->render('observation/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'app_observation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Observation $observation,
        EntityManagerInterface $entityManager
    ): Response {
        // Permissions: alleen eigenaar of admin
        if ($observation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ObservationType::class, $observation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Image upload
            $imageFile = $form->get('imagePath')->getData();
            if ($imageFile) {
                // Oude afbeelding verwijderen als die er is
                if ($observation->getImagePath()) {
                    $oldFilePath = $this->getParameter('kernel.project_dir') . '/public' . $observation->getImagePath();
                    $this->fileUploader->deleteFile($oldFilePath);
                }

                $newFilename = $this->fileUploader->upload(
                    $imageFile,
                    $this->getParameter('uploads_observations_dir')
                );
                $observation->setImagePath('/uploads/observations/' . $newFilename);
            }

            $entityManager->flush();

            $this->addFlash('info', 'Observatie bijgewerkt.');
            return $this->redirectToRoute('app_observation_index');
        }

        return $this->render('observation/edit.html.twig', [
            'observation' => $observation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_observation_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Observation $observation,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response {
        // Permissions check: alleen eigenaar of admin
        if ($observation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // CSRF check
        if ($this->isCsrfTokenValid('delete' . $observation->getId(), $request->request->get('_token'))) {
            // Verwijder bestand via service
            if ($observation->getImagePath()) {
                $filePath = $this->getParameter('kernel.project_dir') . '/public' . $observation->getImagePath();
                $fileUploader->deleteFile($filePath);
            }

            // Verwijder de entity
            $entityManager->remove($observation);
            $entityManager->flush();

            $this->addFlash('danger', 'Observatie verwijderd.');
        }

        return $this->redirectToRoute('app_observation_index');
    }
}
