<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\AvailableVehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home',methods: ['GET','POST'])]
    public function home(Request $request,VehiculeRepository $vehiculeRepository): Response
    {
        $searchForm=$this->createForm(AvailableVehiculeType::class);
        $searchForm->handleRequest($request);
        $availableCars=null;
        $dateDepart=null;
        $dateFin=null;
        if($searchForm->isSubmitted() && $searchForm->isValid())
        {
            $dateDepart=$searchForm->get("date_heure_depart")->getData();
            $dateFin=$searchForm->get("date_heure_fin")->getData();
            $availableCars=$vehiculeRepository->getAvailableCars($dateDepart,$dateFin);
        }
        return $this->render('home/index.html.twig',[
            "searchForm"=> $searchForm,
            "availableCars"=>$availableCars,
            "dateDepart"=>$dateDepart,
            "dateFin"=>$dateFin
        ]);
    }

    #[Route('/reserver/{id}', name: 'app_reserver',methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reserver(Request $request,VehiculeRepository $vehiculeRepository,
        EntityManagerInterface $entityManager,$id): Response
    {
        $dateDepart= \DateTime::createFromFormat('d/m/Y',$request->get("dateDepart"));
        $dateFin= \DateTime::createFromFormat('d/m/Y',$request->get("dateFin"));
        $daysRequired=$dateDepart->diff($dateFin)->days;
        $vehicule=$vehiculeRepository->find($id);
        $commande=new Commande();
        $commande->setDateEnregistrement(new \DateTime());
        $commande->setDateHeureDepart($dateDepart);
        $commande->setDateHeureFin($dateFin);
        $commande->setMembre($this->getUser());
        $commande->setVehicule($vehicule);
        $commande->setPrixTotal($vehicule->getPrixJournalier()*$daysRequired);
        $entityManager->persist($commande);
        $entityManager->flush();
        return $this->redirectToRoute("app_home");
    }
}
