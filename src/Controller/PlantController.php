<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Form\PlantType;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Attribute\Route;

use Knp\Component\Pager\PaginatorInterface;

#[Route('/plant')]
final class PlantController extends AbstractController
{
    #[Route(name: 'app_plant_index', methods: ['GET'])]
    public function index(
        PlantRepository $plantRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $plantRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('plant/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_plant_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $plant = new Plant();
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($slugger->slug($plant->getLatinName()));
            $plant->setSlug($slug);

            $plant->setCreatedAt(new \DateTimeImmutable());
            $plant->setUpdatedAt(new \DateTime());

            $entityManager->persist($plant);
            $entityManager->flush();

            return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plant/new.html.twig', [
            'plant' => $plant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plant_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Plant $plant): Response
    {
        return $this->render('plant/show.html.twig', [
            'plant' => $plant,
        ]);
    }

    #[Route('/plant/{slug}', name: 'app_plant_show_slug', methods: ['GET'])]
    public function showBySlug(string $slug, PlantRepository $repo)
    {
        $plant = $repo->findOneBy(['slug' => $slug]);

        if (!$plant) {
            throw $this->createNotFoundException('Plant not found');
        }

        return $this->render('plant/show.html.twig', [
            'plant' => $plant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_plant_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Plant $plant,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(PlantType::class, $plant);
        $originalLatin = $plant->getLatinName();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plant->setUpdatedAt(new \DateTime());
            // $originalLatin = $existingPlant->getLatinName();

            if ($plant->getLatinName() !== $originalLatin) {

                $slug = strtolower($slugger->slug($plant->getLatinName()));
                $plant->setSlug($slug);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plant/edit.html.twig', [
            'plant' => $plant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plant_delete', methods: ['POST'])]
    public function delete(Request $request, Plant $plant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $plant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($plant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
    }
}
