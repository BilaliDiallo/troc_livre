<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\Commande;
use App\Entity\Categorie;
use App\Entity\Auteur;
use App\Form\PaysType;

use App\Entity\Livre;
use App\Repository\PaysRepository;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pays")
 */
class PaysController extends AbstractController
{
    /**
     * @Route("/", name="pays_index", methods={"GET"})
     */
    public function index(PaysRepository $paysRepository): Response
    {
        return $this->render('pays/index.html.twig', [
            'pays' => $paysRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pays_new", methods={"GET","POST"})
     */
    public function new(Request $request,\Swift_Mailer $mailer): Response
    {
        $pay = new Pays();
        $form = $this->createForm(PaysType::class, $pay);
        $form->handleRequest($request);
        $arry = array();
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $livres = array();
            $categorie = new Categorie();
            $categorie->setNomCategorie($pay->getNomPays());
            $categ =$this->getDoctrine()->getRepository(Categorie::class)->findOneBynomCategorie($categorie->getNomCategorie());
            if(!$categ){
                $this->getDoctrine()->getManager()->persist($categorie);
                $this->getDoctrine()->getManager()->flush();
                
             }
             $categori =$this->getDoctrine()->getRepository(Categorie::class)->findOneBynomCategorie($categorie->getNomCategorie());
            // $categorie->setNomCategorie($form->get('isbn')->getData());
            $livre = $this->getDoctrine()->getRepository(Livre::class)->findBy(['isbn' =>$form->get('id')->getData()], []);
            if(!$livre){
                $array = $this->Livres($form->get('id')->getData());
                if($array=='erreur'){
                    return $this->render('accueil/index.html.twig', [
            
                        'categories' =>array(),
                        'livres' => $livres,
                        'source' =>'ajoutLivre',
                        'error'=>true,
                      
                        
                     
                    ]);
                }
                foreach ($array as $key) {
                    $livre = new Livre();
                    $livre->setIsbn($form->get('id')->getData());
                    $livre->setSaisie(false);
                    $livre->setDisponible(false);
                    $livre->setCategorie($categori);
                    $auteur = new Auteur();
                    $auteur->setNomComplet($key['auteur']);
                    $aut = $this->getDoctrine()->getRepository(Auteur::class)->findOneBynomComplet($auteur->getNomComplet());
                    if(!$aut){
                    $this->getDoctrine()->getManager()->persist($auteur);
                    $this->getDoctrine()->getManager()->flush();
                    
                    }
                    $auteu = $this->getDoctrine()->getRepository(Auteur::class)->findOneBynomComplet($auteur->getNomComplet());

                    $livre->setAuteur($auteu);
                    $livre->setTitre($key['titre']);
                    $livre->setDateDePublication($key['publication']);
                    $livre->setNombrePages($key['pages']);
                    $livre->setLangue($key['langue']);
                    $livre->setImage($key['image']);
                    $point = (int) ($livre->getNombrePages()+100)/100;
                    $livre->setNombrePoints($point);
                    $this->getDoctrine()->getManager()->persist($livre);
                    $this->getDoctrine()->getManager()->flush();
                    
                    $livres[] = $livre;
                }
            }else {
                $livres[] =$livre;
            }

            return $this->render('accueil/index.html.twig', [
            
                'categories' =>array(),
                'livres' => $livres,
                'source' =>'ajoutLivre',
                'error'=>false,
                
            ]);

            // return $this->redirectToRoute('pays_index');
        }
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        return $this->render('accueil/index.html.twig', [
            'pay' => $pay,
            'form' => $form->createView(),
            'source' =>'nouveauLivre',
            'categories'=>$categories,
            'livres' =>array(),
           
        ]);
    }

    /**
     * @Route("/{id}", name="pays_show", methods={"GET"})
     */
    public function show(Pays $pay): Response
    {
        return $this->render('pays/show.html.twig', [
            'pay' => $pay,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pays_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pays $pay): Response
    {
        $form = $this->createForm(PaysType::class, $pay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pays_index');
        }

        return $this->render('pays/edit.html.twig', [
            'pay' => $pay,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pays_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Pays $pay): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pay->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pay);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pays_index');
    }

    





    public function Livres($isbn)
    {   
        // $livre = new Live();
        // $isbn = '0061234001';
        $request = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn;
        try{
            $response = file_get_contents($request);
            $results = json_decode($response);
            $retour = array();
            // $infos =  array();
            // if($results->totalItems > 0){
                // avec de la chance, ce sera le 1er trouvé
                $n = $results->totalItems;
                for ($i=0; $i < $n; $i++) { 
                $infos =  array();
                $book = $results->items[$i];

                if(property_exists($book->volumeInfo,'industryIdentifiers')){
                $infos['isbn'] = $book->volumeInfo->industryIdentifiers[0]->identifier;
                }
                if(property_exists($book->volumeInfo,'title')){
                $infos['titre'] = $book->volumeInfo->title;
                }
                if(property_exists($book->volumeInfo,'authors')){
                $infos['auteur'] = $book->volumeInfo->authors[0];
                }
                //    $infos['category'] = $book->volumeInfo->category;
                if(property_exists($book->volumeInfo,'language')){
                $infos['langue'] = $book->volumeInfo->language;
                }
                if(property_exists($book->volumeInfo,'publishedDate')){
                $infos['publication'] = $book->volumeInfo->publishedDate;
                }

                if(property_exists($book->volumeInfo,'pageCount')){
                $infos['pages'] = $book->volumeInfo->pageCount;
                }

            

                if( property_exists($book->volumeInfo,'imageLinks') ){
                    $infos['image'] = str_replace('&edge=curl', '', $book->volumeInfo->imageLinks->thumbnail);
                }
                if (count($infos) == 7) {
                    $retour[] = $infos;
                }
                
            }
        }catch(\Exception $e){
            return 'erreur';
        }
        return $retour;
    }



    
}
