<?php

namespace App\Controller;

use App\Form\AvailableVehiculeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home',methods: ['GET','POST'])]
    public function home(Request $request): Response
    {
        $searchForm=$this->createForm(AvailableVehiculeType::class);
        $searchForm->handleRequest($request);
        if($searchForm->isSubmitted() && $searchForm->isValid())
        {
            dd($searchForm->get("date_heure_depart")->getData());
        }
        return $this->render('home/index.html.twig',[
            "searchForm"=> $searchForm
        ]);
    }
}
