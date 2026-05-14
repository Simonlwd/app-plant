<?php

namespace App\Controller;

use App\Entity\Observation;
use App\Form\ObservationType;
use App\Repository\ObservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/observation')]
final class ObservationController extends AbstractController
{
    #[Route(name: 'app_observation_index', methods: ['GET'])]
    public function index(ObservationRepository $repo): Response
    {

        $observations = $repo->findBy(
            [],
            ['id' => 'DESC']
        );
        return $this->render('observation/index.html.twig', [
            'observations' => $observations,
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
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/observations',
                    $newFilename
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
}
