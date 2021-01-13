<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Pays;
use App\Entity\Region;
use App\Entity\Commande;
use App\Entity\Departement;
use App\Entity\Commune;
use App\Entity\Auteur;
use App\Entity\Livre;
use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'source' => 'index',
            
        ]);
    }

    /**
     * @Route("/changePiont", name="admin_changePoints",methods={"GET","POST"})
     */
    public function ChangeNombrePoints(Request $request): Response
    {
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        $livre = $this->getDoctrine()->getRepository(Livre::class)->find($_POST['id']);
       if ($livre) {
        $livre->setNombrePoints($_POST['points']);
       
        $this->getDoctrine()->getManager()->flush();
       }else{
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'livres'=>$livres,
            'source'=>'index',
            'action'=>'changePoints',
        ]);
       }
      
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'livres'=>$livres,
            'source'=>'livres',
            'action'=>'changePoints',
        ]);
    }

     /**
     * @Route("/listelivres_chage/{id}", name="admin_list_livre_change")
     */
    public function ListerrrLivre(Request $request, Livre $livre): Response
    {
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        
       

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'livres'=>$livres,
            'source'=>'livres',
            'livre'=>$livre,
            'action'=>'form',
        ]);
    }
  
    /**
     * @Route("/livreprop/{id}", name="livre_proprietaires")
     */
    public function Livres_Prop(Request $request, Livre $livre): Response
    {
       
        $users = $livre->getProprietaire();
       
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users'=>$users,
            'livre'=>$livre,
            'source'=>'listeUsers',
            'action'=>'livre_Prop',
        ]);
    }

    /**
     * @Route("/deletelivre/{id}", name="admin_delete_livre")
     */
    public function deleteLivre(Request $request, Livre $livre): Response
    {
        

        $i =0;
       foreach ($livre->getCommandes() as $com) {
           if($com->getValider() && !$com->getEnvoyer()){
               $i +=1;
           }
        $this->getDoctrine()->getManager()->remove($com);
        $this->getDoctrine()->getManager()->flush();
       }
       if($i !=0 ){
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'livres'=>$livres,
            'source'=>'livres',
            'action'=>'delete',
            'erreur' =>true,
            
        ]);
       }
        foreach ($livre->getCommandes() as $com) {
            if(!$com->getEnvoyer()){
               //Si la commande n'est pas encore envoyer, avant de la supprimer, on rend son commandeur ses points.
                $com->getCommandeur()->addPoints($livre->getNombrePoints);
            }
            $this->getDoctrine()->getManager()->remove($com);
            $this->getDoctrine()->getManager()->flush();
           
        
        }
        $this->getDoctrine()->getManager()->remove($livre);
        $this->getDoctrine()->getManager()->flush();
       
        
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'livres'=>$livres,
            'source'=>'livres',
            'action'=>'delete',
            'erreur'=>false,
            
        ]);
    }

    /**
     * @Route("/listelivres", name="admin_list_livre")
     */
    public function ListerLivre(): Response
    {
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        
       

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'livres'=>$livres,
            'source'=>'livres',
            'action'=>'list',
        ]);
    }

    /**
     * @Route("/listeusers", name="admin_list_User")
     */
    public function ListerUser(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        
       

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users'=>$users,
            'source'=>'listeUsers',
            'action'=>'list',
        ]);
    }

     /**
     * @Route("/nommer/{id}", name="nommer_admin")
     */
    public function nommer_admin(Request $request, User $user): Response
    {
       $user->setRoles(['ROLE_ADMIN']);
       $this->getDoctrine()->getManager()->flush();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
       
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users'=>$users,
             'user'=>$user,
            'source'=>'listeUsers',
            'action'=>'nommer_admin',
        ]);
    }


     /**
     * @Route("/retirer/{id}", name="retirer_admin")
     */
    public function retirer_admin(Request $request, User $user): Response
    {
       $user->RevoqueRoles('ROLE_ADMIN');
       $this->getDoctrine()->getManager()->flush();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
       
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users'=>$users,
             'user'=>$user,
            'source'=>'listeUsers',
            'action'=>'nommer_admin',
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer_user")
     */
     public function Supprimer_User(Request $request, User $user): Response
     {  
        foreach ($user->getEnvoies() as $commande) {
            $this->getDoctrine()->getManager()->remove($commande);
            $this->getDoctrine()->getManager()->flush();
        }
        foreach ($user->getCommandes() as $commande) {
            $this->getDoctrine()->getManager()->remove($commande);
            $this->getDoctrine()->getManager()->flush();
        }
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
         $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        
         return $this->render('admin/index.html.twig', [
            
             'users'=>$users,
              'user'=>$user,
             'source'=>'listeUsers',
             'action'=>'supprimer_user',
         ]);
     }















    /**
     * @Route("/listeCommandes", name="admin_list_Commandes")
     */
    public function ListerCommandes(): Response
    {
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();
        
       

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'commandes'=>$commandes,
            'source'=>'listeCommandes',
            'action'=>'list',
        ]);
    }
}
