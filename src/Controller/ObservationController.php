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
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            1
        );
        return $this->render('observation/index.html.twig', [
            'pagination' => $pagination,
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
        $form = $this->createForm(ObservationType::class, $observation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_observation_index');
        }

        return $this->render('observation/edit.html.twig', [
            'observation' => $observation,
            'form' => $form,
        ]);
    }
}
