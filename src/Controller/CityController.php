<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\City;
use App\Form\ActivityType;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ville', name: 'city_')]
class CityController extends AbstractController
{

    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $city = new City();
        $cityForm = $this->createForm(CityType::class, $city);
        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()) {
            $entityManager->persist($city);
            $entityManager->flush();
            $this->addFlash('success', 'Une ville a bien été ajouté');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('city/create.html.twig', [
            'cityForm' => $cityForm->createView()
        ]);
    }

}
