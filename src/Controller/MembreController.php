<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\MembreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembreController extends AbstractController
{
    #[Route('/profil', name: 'app_membre')]
    public function index(): Response
    {
        $user=$this->getUser();
        $profileForm=$this->createForm(MembreType::class,$user);
        return $this->render('membre/index.html.twig', [
            'user'=>$user,
            'profilForm'=>$profileForm
        ]);
    }
}
