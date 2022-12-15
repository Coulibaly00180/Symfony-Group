<?php

namespace App\Controller;

use App\Entity\Espace;
use App\Form\EspaceType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EspaceController extends AbstractController
{
    /**
     * @Route("/", name="app_espace")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Espace::class);
        $espaces = $repository->findAll();

        return $this->render('espace/index.html.twig', [
            'espaces' => $espaces,
        ]);
    }


    /**
     * @Route("/espace/ajouter", name="app_espace_ajouter")
     */
    public function ajouter(ManagerRegistry $doctrine, Request $request ): Response
    {
        $espace = new Espace();

        $form = $this->createForm(EspaceType::class, $espace);

        $form->handleRequest($request);

        $dataOuvre = $form->getData()->getDateOuverture();
        $dataFerme = $form->getData()->getDateFermeture();

        if ($form->isSubmitted() && $form->isValid()){

            if($dataOuvre < $dataFerme){
                $em = $doctrine->getManager();

                $em->persist($espace);

                $em->flush();

                return $this->redirectToRoute("app_espace");
            }
            else{
                return $this->render("espace/ajouter.html.twig", [
                    "formulaire" => $form->createView(),
                    "Message" => "La date d'ouverture est plus grande que la date de fermeture, veuillez modifier !"
                ]);
            }
        }
        return $this->render("espace/ajouter.html.twig",[
            "formulaire" => $form->createView()
         ]);

    }

    /**
     * @Route("espace/modifier/{id}" , name="app_espace_modifier")
     */
    public function modifier($id, ManagerRegistry $doctrine, Request $request ):Response {
        
        $espace = $doctrine->getRepository(Espace::class)->find($id);

        if (!$espace){
            throw $this->createNotFoundException("Pas de catégorie avec l'id $id");
        }

        $form=$this->createForm(EspaceType::class, $espace);
        $form->handleRequest($request);

        $dataOuvre = $form->getData()->getDateOuverture();
        $dataFerme = $form->getData()->getDateFermeture();

        if ($form->isSubmitted() && $form->isValid()){
            if($dataOuvre < $dataFerme){
                $em = $doctrine->getManager();

                $em->persist($espace);

                $em->flush();

                return $this->redirectToRoute("app_espace");
            }

            //on revient à l'accueil
            return $this->redirectToRoute("app_espace");
        }
        else{
            return $this->render("espace/ajouter.html.twig", [
                "formulaire" => $form->createView(),
                "Message" => "La date d'ouverture est plus grande que la date de fermeture, veuillez modifier !"
            ]);
        }

        return $this->render("espace/modifier.html.twig",[
            "espace"=>$espace,
            "formulaire"=>$form->createView()
        ]);

    }

    /**
     * @Route("/espace/supprimer/{id}", name="app_espace_supprimer")
     */
    public function supprimer($id, ManagerRegistry $doctrine, Request $request): Response{

        $espace = $doctrine->getRepository(espace::class)->find($id);

        if (!$espace){
            throw $this->createNotFoundException("Pas d'espace avec l'id $id");
        }

        $form = $this->createForm(EspaceSupprimerType::class, $espace);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $em=$doctrine->getManager();

            $em->remove($espace);

            $em->flush();

            return $this->redirectToRoute("app_espace");
        }

        return $this->render("espace/supprimer.html.twig",[
            "espace"=>$espace,
            "formulaire"=>$form->createView()
        ]);
    }

}
