<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\ConnexionType;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion',methods: ['GET','POST'])]
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        $error=$authenticationUtils->getLastAuthenticationError();
        $lastPseudo=$authenticationUtils->getLastUsername();
        return $this->render('connexion/connexion.html.twig', [
            'controller_name' => 'ConnexionController',
            'last_username'=> $lastPseudo,
            'error'=> $error
        ]);
    }

    #[Route('/register',name:'app_register')]
    public function register(Request $request,UserPasswordHasherInterface $passwordHasher,
                             EntityManagerInterface $entityManager)
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


    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


}
