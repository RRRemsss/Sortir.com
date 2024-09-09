<?php

namespace App\Controller;

use App\Entity\Status;
use App\Form\StatusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/status', name: 'status_')]
class StatusController extends AbstractController
{

    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = new Status();
        $statusForm = $this->createForm(StatusType::class, $status);
        $statusForm->handleRequest($request);

        if ($statusForm->isSubmitted() && $statusForm->isValid()) {
            $entityManager->persist($status);
            $entityManager->flush();
            $this->addFlash('success', 'Un status a bien été ajouté');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('status/create.html.twig', [
            'placeForm' => $statusForm->createView()
        ]);
    }
}
