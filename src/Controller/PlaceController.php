<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lieu', name: 'place_')]
class PlaceController extends AbstractController
{

    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $place = new Place();
        $placeForm = $this->createForm(PlaceType::class, $place);
        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            $entityManager->persist($place);
            $entityManager->flush();
            $this->addFlash('success', 'Un lieu a bien été ajouté');
            return $this->redirectToRoute('sortie_create');
        }

        return $this->render('place/create.html.twig', [
            'placeForm' => $placeForm->createView()
        ]);
    }
}
