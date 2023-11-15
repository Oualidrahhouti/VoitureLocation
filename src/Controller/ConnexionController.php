<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(): Response
    {
        return $this->render('connexion/connexion.html.twig', [
            'controller_name' => 'ConnexionController',
        ]);
    }

    #[Route('/register',name:'app_register')]
    public function register(Request $request,UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $entityManager)
    {
        $membre=new Membre();
        $membre->setDateEnregistrement(new \DateTime());
        $registerForm=$this->createForm(RegisterType::class,$membre);
        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $plainTextPassword=$membre->getMdp();
            $membre->setMdp($passwordHasher->hashPassword($membre,$plainTextPassword));
            $entityManager->persist($membre);
            $entityManager->flush();
        }
        return $this->render('connexion/register.html.twig',[
            'form'=>$registerForm
        ]);
    }

}
