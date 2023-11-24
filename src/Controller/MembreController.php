<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\MembreType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembreController extends AbstractController
{
    #[Route('/profil', name: 'app_membre')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $myRents=$commandeRepository->findBy(["membre"=>$this->getUser()]);
        $user=$this->getUser();
        $profileForm=$this->createForm(MembreType::class,$user);
        return $this->render('membre/index.html.twig', [
            'user'=>$user,
            'profilForm'=>$profileForm,
            'commandes'=>$myRents
        ]);
    }
}
