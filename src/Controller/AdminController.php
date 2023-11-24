<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\CommandeRepository;
use App\Repository\MembreRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes=$commandeRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'commandes'=> $commandes
        ]);
    }

    #[Route('/vehicule', name: 'app_add_vehicule',methods: ['GET','POST'])]
    public function AjouterVehicule(Request $request,SluggerInterface $slugger,
    EntityManagerInterface $entityManager): Response
    {
        $vehicule=new Vehicule();
        $vehicule->setDateEnregistrement(new \DateTime());
        $vehiculeForm=$this->createForm(VehiculeType::class,$vehicule);
        $vehiculeForm->handleRequest($request);
        if( $vehiculeForm->isSubmitted() && $vehiculeForm->isValid())
        {
            $vehiculeImage=$vehiculeForm->get('photo')->getData();
            $newFileName=null;
            if($vehiculeImage)
            {
                $originalFileName=pathinfo($vehiculeImage->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFileName=$slugger->slug($originalFileName);
                $newFileName='Vehicule-'.$safeFileName.'-'.uniqid().'.'.$vehiculeImage->guessExtension();
                try {
                    $vehiculeImage->move(
                        $this->getParameter('images_directory'),
                        $newFileName
                    );
                }catch (FileException $e)
                {
                    $this->redirectToRoute("app_home");
                }
                $vehicule->setPhoto($newFileName);
            }
            $entityManager->persist($vehicule);
            $entityManager->flush();
        }
        return $this->render('admin/ajouter_vehicule.html.twig', [
            'vehiculeForm'=> $vehiculeForm
        ]);
    }

    #[Route('/vehicule/{id}', name: 'app_edit_vehicule',methods: ['GET','POST'])]
    public function ModifierVehicule(VehiculeRepository $vehiculeRepository,$id): Response
    {
        $vehicule=$vehiculeRepository->find($id);
        $vehiculeForm=$this->createForm(VehiculeType::class,$vehicule);
        return $this->render('admin/modifier_vehicule.html.twig', [
            'vehiculeForm'=> $vehiculeForm
        ]);
    }
    #[Route('/vehicule/remove/{id}', name: 'app_remove_vehicule')]
    public function SupprimerVehicule(VehiculeRepository $vehiculeRepository,EntityManagerInterface $entityManager, $id=0): Response
    {
        $vehicule=$vehiculeRepository->find($id);
        if($vehicule) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_vehicules");
    }
    #[Route('/vehicules', name: 'app_vehicules',methods: ['GET','POST'])]
    public function vehicules(VehiculeRepository $vehiculeRepository): Response
    {
        $vehicules=$vehiculeRepository->findAll();
        return $this->render('admin/vehicules.html.twig', [
            'vehicules'=> $vehicules
        ]);
    }
    #[Route('/membres', name: 'app_membres',methods: ['GET','POST'])]
    public function membres(MembreRepository $membreRepository): Response
    {
        $clients=$membreRepository->findAll();
        $clients=array_filter($clients,function ($crt){
            return !in_array('ROLE_ADMIN',$crt->getRoles());
        });
        return $this->render('admin/clients.html.twig', [
            "clients"=>$clients
        ]);
    }
    #[Route('/membres/delete/{id}', name: 'app_remove_membres',methods: ['POST'])]
    public function SupprimerMembre(MembreRepository $membreRepository,
                                    EntityManagerInterface $entityManager,$id): Response
    {
        $client=$membreRepository->find($id);
        if($client)
        {
            $entityManager->remove($client);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_membres");
    }

}
