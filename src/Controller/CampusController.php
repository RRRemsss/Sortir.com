<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{

    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $campusForm =$this->createForm(CampusType::class,$campus);
        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()){
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->addFlash('success','Un campus a bien été ajouté');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('campus/create.html.twig',[
            'campusForm'=>$campusForm->createView()
        ]);
    }

}
