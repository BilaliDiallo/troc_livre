<?php

namespace App\Controller;
use App\Entity\Auteur;
use App\Entity\Livre;
use App\Entity\Categorie;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AccueilController extends AbstractController
{
   
    /**
     * @Route("/accueil", name="accueil")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'categories' =>$categories,
            'livres' => $livres,
            'error' =>$error,
            'last_username'=>$lastUsername,
            'source' =>'index',
           
           
        ]);
    }


    public function Nettoie( $str ) {
        $charset='utf-8';
        $str = htmlentities( $str, ENT_NOQUOTES, $charset );
        
        $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
        $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
        $str = preg_replace( '#&[^;]+;#', '', $str );
        
        return strtoupper($str);
    }

   

     /**
     * @Route("/mesLivres", name="mes_livres")
     */
    public function MesLivres(): Response
    {
    
       
    
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $categories = array_reverse($categories);
      

            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                'categories' =>$categories,
                'livres' => $this->getUser()->getAvoir(),
               
                'source' =>'mesLivre',
                
            ]);
    }

     /**
     * @Route("/recherche", name="moteur_recherche", methods={"GET","POST"})
     */
    public function Recherche(Request $request): Response
    {
            $nom = $_POST['text'];
            $livres = array();
            $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
            $livres = $this->getDoctrine()->getRepository(Livre::class)->findBy(['titre'=>$nom],[]);
            if(!$livres){
                $auteur = $this->getDoctrine()->getRepository(Auteur::class)->findOneBy(['nomComplet' =>$nom],[]);
                if($auteur){
                    $livres = $auteur->getLivresEcrit();
                }
            }
            if (count($livres)==0) {
                $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findOneBy(['nomCategorie' =>$nom],[]);
                if($auteur){
                    $livres = $categorie->getLivres();
                }
            }

            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                'categories' =>$categories,
                'livres' => $livres,
                'source' =>'re_cher_che',
              
               
                
            ]);
        
    }


    /**
     * @Route("/categorie/{id}", name="categorie_livres")
     */
    public function categorie_livres(Request $request, Categorie $categorie): Response
    {

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $categories = array_reverse($categories);
      

            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                'categories' =>$categories,
                'livres' => $categorie->getLivres(),
                'source' =>'categorieLivre',
              
               
                
            ]);
    }

     /**
     * @Route("/auteurLivres/{id}", name="auteur_livres")
     */
    public function auteur_livres(Request $request, Auteur $auteur): Response
    {

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        
      

            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                'categories' =>$categories,
                'livres' => $auteur->getLivresEcrit(),
                'source' =>'auteurLivre',
                
               
                
            ]);
    }



    /**
     * @Route("/", name="homepage")
     */
    public function homepage(): Response
    {
            
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'categories' =>$categories,
            'livres' => $livres,
            'source' =>'homepages',
           
            
           
        ]);
    }

    // Ajouter un livre 

    /**
     * @Route("/ajouter/{id}", name="ajouter_livres")
     */
    public function ajouter_livres(Request $request, Livre $livre): Response
    {
        

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        
        if($this->getUser() == null){
            
             return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                'categories' =>$categories,
                'livres' => $livres,
                
                'source' =>'execption',
            ]);
        }else{
                
            $this->getUser()->addAvoir($livre);
            $livre->setDisponible(true);
                //  $this->getDoctrine()->getManager()->persist($livre);
                $this->getDoctrine()->getManager()->flush();
            
        
                return $this->render('accueil/index.html.twig', [
                    'controller_name' => 'AccueilController',
                    'categories' =>$categories,
                    'livres' => $livres,
                    
                    'source' =>'ajouterLivre',
                   
                
                    
                ]);
            }
           
    }



     /**
     * @Route("/supprimer/{id}", name="supprimer_livres")
     */
    public function Supprimer_livres(Request $request, Livre $livre): Response
    {
        

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        
        // if(in_collection($this->getUser(),$livre->getProprietaire())){
        //     $livres=[];
        // }else{
            $this->getUser()->removeAvoir($livre);
        
            //  $this->getDoctrine()->getManager()->persist($livre);
             $this->getDoctrine()->getManager()->flush();
        
            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                'categories' =>$categories,
                'livres' => $this->getUser()->getAvoir(),
               
                'source' =>'supprimerLivre',
              
               
                
            ]);
    }



    /**
     * @Route("/infos/{id}", name="infos_livres")
     */
    public function infos_livres(Request $request, Livre $livre): Response
    {

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        
        // if(in_collection($this->getUser(),$livre->getProprietaire())){
        //     $livres=[];
        // }else{
        
            //  $this->getDoctrine()->getManager()->persist($livre);
            // $this->getDoctrine()->getManager()->flush();
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findBy(array('auteur' => $livre->getAuteur()->getNomComplet(), 'categorie' => $livre->getCategorie()->getNomCategorie()),array('id' => 'desc'));
      
            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                'categories' =>$categories,
                'livres' => $livres,
                'livre' =>$livre,
                'source' =>'infosLivre',
               
                
            ]);
    }












    // https://numa-bord.com/miniblog/php-google-map-api-recuperer-coordonees-gps-depuis-adresse-format-humain/


}








