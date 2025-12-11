<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CarController extends AbstractController
{
    // PAGE D’ACCUEIL : /
    #[Route('/', name: 'app_car_index', methods: ['GET'])]
    public function index(CarRepository $carRepository): Response
    {
        return $this->render('car/accueil.html.twig', [
            'cars' => $carRepository->findAll(),
        ]);
    }

    // /car/new
    #[Route('/car/new', name: 'app_car_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

            return $this->redirectToRoute('app_car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    // /car/{id}
    #[Route('/car/{id}', name: 'app_car_show', methods: ['GET'])]
    public function show(Car $car): Response
    {
        return $this->render('car/show.html.twig', [
            'car' => $car,
        ]);
    }

    // /car/{id}/edit
    #[Route('/car/{id}/edit', name: 'app_car_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    
    // DELETE : /voiture/{id}/supprimer
    #[Route('/voiture/{id}/supprimer', name: 'app_car_delete', methods: ['GET'])]
    public function delete(int $id, CarRepository $carRepository, EntityManagerInterface $entityManager): Response
    {
        // 1. On récupère la voiture par son id
        $car = $carRepository->find($id);

        // 2. Si aucune voiture trouvée → on retourne à l’accueil
        if (!$car) {
            // tu peux ajouter un flash si tu veux informer l’utilisateur
            // $this->addFlash('warning', 'Cette voiture n’existe pas.');
            return $this->redirectToRoute('app_car_index');
        }

        // 3. On supprime la voiture
        $entityManager->remove($car);
        $entityManager->flush();

        // 4. On retourne sur la page d’accueil
        // $this->addFlash('success', 'La voiture a bien été supprimée.');
        return $this->redirectToRoute('app_car_index');
    }
}
