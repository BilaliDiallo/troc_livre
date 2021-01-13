<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\Envoie;
use App\Form\LivreType;
use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/livre")
 */
class LivreController extends AbstractController
{
    /**
     * @Route("/", name="livre_index", methods={"GET"})
     */
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="livre_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $livre = new Livre();
        $auteur = new Auteur();
        $categorie = new Categorie();
       // $envoie = new Envoie();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $auteur->setNomComplet($form->get('auteur')->getData());
            $categorie->setNomCategorie($form->get('categorie')->getData());
            $categ =$this->getDoctrine()->getRepository(Categorie::class)->findOneBynomCategorie($categorie->getNomCategorie());
            $aut = $this->getDoctrine()->getRepository(Auteur::class)->findOneBynomComplet($auteur->getNomComplet());
            if(!$aut){
               $this->getDoctrine()->getManager()->persist($auteur);
               $this->getDoctrine()->getManager()->flush();
               
            }
            $autt = $this->getDoctrine()->getRepository(Auteur::class)->findOneBynomComplet($auteur->getNomComplet());
            if(!$categ){
                $this->getDoctrine()->getManager()->persist($categorie);
                $this->getDoctrine()->getManager()->flush();
                
             }
            $categori =$this->getDoctrine()->getRepository(Categorie::class)->findOneBynomCategorie($categorie->getNomCategorie());
            $livre->setAuteur($autt);
            $livre->setCategorie($categori);
            $fichier = md5(uniqid()).'.'.$image->guessExtension();
            $image->move(
                $this->getParameter('stock_image'),
                $fichier
            );
             $livre->setImage($fichier);
             $point = (int) ($livre->getNombrePages()+100)/100;
             $livre->setNombrePoints($point);
             $livre->setDisponible(true);
             $livre->setSaisie(true);
             
             $livre->addProprietaire($this->getUser());
          
           //  $livre->setEnvoyer($envoie);
             $this->getDoctrine()->getManager()->persist($livre);
             $this->getDoctrine()->getManager()->flush();
            //  $this->getUser()->addAvoir($livre);
            //  $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('livre_index');
        }

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        return $this->render('accueil/index.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
            'source' =>'newLivre',
            'categories'=>$categories,
            'livres' =>array(),
           
        ]);
    }

    /**
     * @Route("/{id}", name="livre_show", methods={"GET"})
     */
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="livre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Livre $livre): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        $auteur = new Auteur();
        $categorie = new Categorie();

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            $auteur->setNomComplet($form->get('auteur')->getData());
            $categorie->setNomCategorie($form->get('categorie')->getData());
            $categ =$this->getDoctrine()->getRepository(Categorie::class)->findOneBynomCategorie($categorie->getNomCategorie());
            $aut = $this->getDoctrine()->getRepository(Auteur::class)->findOneBynomComplet($auteur->getNomComplet());
            if(!$aut){
               $this->getDoctrine()->getManager()->persist($auteur);
               $this->getDoctrine()->getManager()->flush();
               
            }
            $autt = $this->getDoctrine()->getRepository(Auteur::class)->findOneBynomComplet($auteur->getNomComplet());
            if(!$categ){
                $this->getDoctrine()->getManager()->persist($categorie);
                $this->getDoctrine()->getManager()->flush();
                
             }
            $categori =$this->getDoctrine()->getRepository(Categorie::class)->findOneBynomCategorie($categorie->getNomCategorie());
            $livre->setAuteur($autt);
            $livre->setSaisie(true);
            $livre->setCategorie($categori);
            $fichier = md5(uniqid()).'.'.$image->guessExtension();
            $image->move(
                $this->getParameter('stock_image'),
                $fichier
            );
            $livre->setImage($fichier);
            // $livre->getEnvoyer()->setNom($fichier);
             $point = (int) ($livre->getNombrePages()+100)/100;
             $livre->setNombrePoints($point);
             $livre->setDisponible(true);
             $livre->addProprietaire($this->getUser());
            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mes_livres');
        }

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        return $this->render('accueil/index.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
            'source' =>'modifLivre',
            'categories'=>$categories,
            'livres' =>array(),
           
        ]);
    }

    /**
     * @Route("/{id}", name="livre_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Livre $livre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livre_index');
    }

   

   
}
