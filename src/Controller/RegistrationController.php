<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Pays;
use App\Entity\Region;
use App\Entity\Livre;
use App\Entity\Commande;
use App\Entity\Departement;
use App\Entity\Commune;
use App\Entity\Categorie;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setPoints(4);
            $user->setPointsEmprunter(0);
            $user->setVacance(false);
            $pays =$this->Nettoie($form->get('pays')->getData());
            $pay = $this->getDoctrine()->getRepository(Pays::class)->findOneBynomPays($pays);
            if(!$pay){
                $pay = new Pays();
                $pay->setNomPays($pays);
                $this->getDoctrine()->getManager()->persist($pay);
               $this->getDoctrine()->getManager()->flush();
            }$pay = $this->getDoctrine()->getRepository(Pays::class)->findOneBynomPays($pays);
            $user->setPays($pay);

            $region = $this->Nettoie($form->get('region')->getData());
            $reg = $this->getDoctrine()->getRepository(Region::class)->findOneBynomRegion($region);
            if(!$reg){
                $reg = new Region();
                $reg->setNomRegion($region);
                $this->getDoctrine()->getManager()->persist($reg);
                $this->getDoctrine()->getManager()->flush();
             }$reg = $this->getDoctrine()->getRepository(Region::class)->findOneBynomRegion($region);
            $user->setRegion($reg);

            $departement= $this->Nettoie($form->get('departement')->getData());
            $reg = $this->getDoctrine()->getRepository(Departement::class)->findOneBynomDepartement($departement);
            if(!$reg){
                $reg = new Departement();
                $reg->setNomDepartement($departement);
                $this->getDoctrine()->getManager()->persist($reg);
                $this->getDoctrine()->getManager()->flush();
             }$reg = $this->getDoctrine()->getRepository(Departement::class)->findOneBynomDepartement($departement);
            $user->setDepartement($reg);

            $commune = $this->Nettoie($form->get('commune')->getData());
            $reg = $this->getDoctrine()->getRepository(Commune::class)->findOneBynomCommune($commune);
            if(!$reg){
                $reg = new Commune();
                $reg->setNomCommune($commune);
                $this->getDoctrine()->getManager()->persist($reg);
                $this->getDoctrine()->getManager()->flush();
             }$reg = $this->getDoctrine()->getRepository(Commune::class)->findOneBynomCommune($commune);
            $user->setCommune($reg);
            $user->setCodePostal('');
            $user->setNewDemande(0);
            $user->setNewEnvoie(0);

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
           
            return $this->render('accueil/index.html.twig', [
                'registrationForm' => $form->createView(),
                'source' =>'message_bienvenue',
                'categories'=>$categories,
                'livres' =>array(),
                'user' =>$user,
               
            ]);
            
        }
        // xmymysxdwlrhxjtw
        
        return $this->render('accueil/index.html.twig', [
            'registrationForm' => $form->createView(),
            'source' =>'user_inscription',
            'categories'=>$categories,
            'livres' =>array(),
           
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
     * @Route("/passwordOublie", name="motdepassOublie")
     */
    public function MotDePasseOubler(Request $request,UserPasswordEncoderInterface $passwordEncoder ): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $alert ="Email introuvable !!!! Recommancez en saisissant un email correcte. Ou si vous n'avez pas de compte, inscrivez-vous.";
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByemail($_POST['email']);
        if($user){
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $_POST['password']
                )
            );
            $this->getDoctrine()->getManager()->flush();
            $alert = $user->getCivilite()." ".$user->getPrenom()." ".$user->getNom()." votre nouveau mot de passe est ".$_POST['password'].".Vous pouvez vous connecter avec. Retenez qu'un mot mot de passe doit rester secret.";
        }
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
             'alert' =>$alert,
           
            'source' =>'mot_de_pass_oublien',    
        ]);
    }


    /**
     * @Route("/modifier_compte", name="modifier_compte")
     */
    public function Modifier(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $pays =$this->Nettoie($form->get('pays')->getData());
            $pay = $this->getDoctrine()->getRepository(Pays::class)->findOneBynomPays($pays);
            if(!$pay){
                $pay = new Pays();
                $pay->setNomPays($pays);
                $this->getDoctrine()->getManager()->persist($pay);
               $this->getDoctrine()->getManager()->flush();
            }$pay =  $this->getDoctrine()->getRepository(Pays::class)->findOneBynomPays($pays);
            $user->setPays($pay);

            $region = $this->Nettoie($form->get('region')->getData());
            $reg = $this->getDoctrine()->getRepository(Region::class)->findOneBynomRegion($region);
            if(!$reg){
                $reg = new Region();
                $reg->setNomRegion($region);
                $this->getDoctrine()->getManager()->persist($reg);
                $this->getDoctrine()->getManager()->flush();
             }$reg = $this->getDoctrine()->getRepository(Region::class)->findOneBynomRegion($region);
            $user->setRegion($reg);

            $departement= $this->Nettoie($form->get('departement')->getData());
            $reg = $this->getDoctrine()->getRepository(Departement::class)->findOneBynomDepartement($departement);
            if(!$reg){
                $reg = new Departement();
                $reg->setNomDepartement($departement);
                $this->getDoctrine()->getManager()->persist($reg);
                $this->getDoctrine()->getManager()->flush();
             }$reg = $this->getDoctrine()->getRepository(Departement::class)->findOneBynomDepartement($departement);
            $user->setDepartement($reg);

            $commune = $this->Nettoie($form->get('commune')->getData());
            $reg = $this->getDoctrine()->getRepository(Commune::class)->findOneBynomCommune($commune);
            if(!$reg){
                $reg = new Commune();
                $reg->setNomCommune($commune);
                $this->getDoctrine()->getManager()->persist($reg);
                $this->getDoctrine()->getManager()->flush();
             }$reg = $this->getDoctrine()->getRepository(Commune::class)->findOneBynomCommune($commune);
            $user->setCommune($reg);
            

          
            $this->getDoctrine()->getManager()->flush();
           
            return $this->render('accueil/index.html.twig', [
               
                'source' =>'message_modifier',
                'categories'=>$categories,
                'livres' =>array(),
                'user' =>$user,
               
            ]);
            
        }
        // xmymysxdwlrhxjtw
        
        return $this->render('accueil/index.html.twig', [
            'registrationForm' => $form->createView(),
            'source' =>'user_modification',
            'categories'=>$categories,
            'livres' =>array(),
           
        ]);
    }



    /**
     * @Route("/ap_commander/{id}", name="app_commander")
     */
    public function Commander(Request $request, Livre $livre): Response
    {   
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();

        if($this->getUser() != null){

            $commander = new User();
            $resultat = true;
            $User = $this->getUser();
            $alert ='';
            if($User->getVacance()){
                $resultat = false;
                $alert ="Impossible de faire des commandes en étant en mode vacance.Si vous voulez faire des commandes, veuillez désactiver le mode vacance.";
            }else{
            if($User->getPoints() >= $livre->getNombrePoints()){
                $pays = array(); $region=array(); $dep =array(); $commune=array();
                foreach( $livre->getProprietaire() as $user ){
                    if(!$user->getVacance() && $user->getPays()===$User->getPays() && $user->getRegion()===$User->getRegion() && $user->getDepartement()===$User->getDepartement() && $user->getCommune()===$User->getCommune()){
                        if($user != $User ){
                            $commune[] =$user;
                            break;
                        }else {
                            $alert ='Vous disposer déjà de ce livre';
                            $resultat = false;
                            break;
                        }
                        
                    }elseif (!$user->getVacance() && $user != $User && $user->getPays()===$User->getPays() && $user->getRegion()===$User->getRegion() && $user->getDepartement()===$User->getDepartement()) {
                        $dep[] =$user;
                    }elseif (!$user->getVacance() && $user != $User && $user->getPays()===$User->getPays() && $user->getRegion()===$User->getRegion()) {
                        $region[] =$user;
                    }elseif (!$user->getVacance() && $user != $User && $user->getPays()===$User->getPays()) {
                        $pays[] =$user;
                    }
                }
                if (count($commune) != 0) {
                    $commander = $commune[0];
                }elseif (count($dep) != 0) {
                    $commander = $dep[0];
                }elseif (count($region) != 0) {
                    $commander = $region[0];
                }elseif (count($pays) != 0) {
                    $commander = $pays[0];
                }else {
                    if($alert !='Vous disposer déjà de ce livre'){
                        $alert = "Ce livre est indisponible.";
                    }
                    $resultat = false;
                }
            }else {
                $alert ="Vous n'avez pas suffisamment de points pour commander se livre.";
                $resultat = false;
            }
        }

            if($resultat){
                $commande = new Commande();
                $commande->setEnvoyer(false);
                $commande->setValider(false);
                $commande->setSupprimee(false);
                $commande->setLivre($livre);
                $commande->setRefuser(false);
                $commande->setCommandeur($this->getUser());
                $commande->setEnvoyeur($commander);
                $commander->addNewDemande(1);
                $commande->setDate(date('d-m-Y'));
                $user->removeAvoir($livre);
                
                $this->getUser()->setPoints($this->getUser()->getPoints() - $livre->getNombrePoints());
                $this->getDoctrine()->getManager()->persist($commande);
                $this->getDoctrine()->getManager()->flush();
            }

            
                return $this->render('accueil/index.html.twig', [
                
                    'categories' =>$categories,
                    'livres' => $livres,
                
                    'source' =>'commanderLivre',
                    'message' =>$alert,
                    'resultat'=>$resultat,
                    
                
                    
                ]);
        
            }else {
                return $this->render('accueil/index.html.twig', [
                
                    'categories' =>$categories,
                    'source' =>'execption',
                    'livres' => $livres,]);
                   
            }
    }

    /**
     * @Route("/command/{id}", name="app_validerCom")
     */
    public function ValiderCommande(Request $request, Commande $commande): Response
    {
        $commande->setValider(true);
        $commande->setRefuser(false);
        $commande->setDateValidation(date('d-m-Y'));
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        
        $this->getDoctrine()->getManager()->flush();
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => 'CommandeConfirme',
           
            'source' =>'mesDemandes',
          
             
           
            
        ]);
    }

    /**
     * @Route("/refuse_command/{id}", name="app_refuserCom")
     */
    public function RefuserCommande(Request $request, Commande $commande): Response
    {
        $commande->setRefuser(true);
        $commande->setValider(false);
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        $this->getDoctrine()->getManager()->flush();
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => 'refugercommande',
           
            'source' =>'mesDemandes',
          
             
           
            
        ]);
    }

    /**
     * @Route("/confirme_command/{id}", name="app_confirmeCom")
     */
    public function CommandeRecu(Request $request, Commande $commande): Response
    {
        $commande->setEnvoyer(true);
        $commande->setLivraison(date('d-m-Y'));
        $pointEmprunte = $commande->getEnvoyeur()->getPointsEmprunter();
        $points = $commande->getLivre()->getNombrePoints();
        if($pointEmprunte > 0){
            if($pointEmprunte > $points){
                if($points>2 ){
                    $commande->getEnvoyeur()->addPoints($points - 2);
                    $commande->getEnvoyeur()->setPointsEmprunter($pointEmprunte-2);

                }else{
                    $commande->getEnvoyeur()->setPointsEmprunter($pointEmprunte-$points);
                }
            }else{
                $commande->getEnvoyeur()->setPointsEmprunter(0);
                $commande->getEnvoyeur()->addPoints($points - $pointEmprunte);
            }
            
        }
        $commande->getEnvoyeur()->addNewEnvoie(1);
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        
        $this->getDoctrine()->getManager()->flush();
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => 'confirm',
            'commande'=>$commande,
            'source' =>'mesCommandes',
          
           
            
        ]);
    }

    /**
     * @Route("/signale_command/{id}", name="app_SignalerCom")
     */
    public function SignalerCommande(Request $request, Commande $commande): Response
    {
        $commande->setEnvoyer(false);
        $commande->setValider(false);
        $commande->setRefuser(false);
        if($commande->getEnvoyeur()->getNewDemande()>0){
            $commande->getEnvoyeur()->setNewDemande($commande->getEnvoyeur()->getNewDemande() -1);
        }
        $commande->getCommandeur()->addPoints($commande->getLivre()->getNombrePoints());
        $alert='';
        $resultat =true;
        $commander = new Commande();
        $pays = array(); $region=array(); $dep =array(); $commune=array();
            foreach( $commande->getLivre()->getProprietaire() as $user ){
                if(!$user->getVacance() && $user->getPays()===$User->getPays() && $user->getRegion()===$User->getRegion() && $user->getDepartement()===$User->getDepartement() && $user->getCommune()===$User->getCommune()){
                    if($user != $User ){
                        $commune[] =$user;
                        break;
                    }else {
                        $alert ='Vous disposer déjà de ce livre';
                        $resultat = false;
                        break;
                    }
                    
                }elseif (!$user->getVacance() && $user != $User && $user->getPays()===$User->getPays() && $user->getRegion()===$User->getRegion() && $user->getDepartement()===$User->getDepartement()) {
                    $dep[] =$user;
                }elseif (!$user->getVacance() && $user != $User && $user->getPays()===$User->getPays() && $user->getRegion()===$User->getRegion()) {
                    $region[] =$user;
                }elseif (!$user->getVacance() && $user != $User && $user->getPays()===$User->getPays()) {
                    $pays[] =$user;
                }
            }
            if (count($commune) != 0) {
                $commander = $commune[0];
            }elseif (count($dep) != 0) {
                $commander = $dep[0];
            }elseif (count($region) != 0) {
                $commander = $region[0];
            }elseif (count($pays) != 0) {
                $commander = $pays[0];
            }else {
                if($alert !='Vous disposer déjà de ce livre'){
                    $alert = "Ce livre est indisponible.";
                }
                $resultat = false;
            }
        
        $commande->getLivre()->addProprietaire($commande->getEnvoyeur());
        if($resultat){
            

            $commande->setEnvoyer(false);
            $commande->setValider(false);
           
            $commande->setRefuser(false);
            $commander->addNewDemande(1);
            $commande->setEnvoyeur($commander);
            $commande->setDate(date('d-m-Y'));
            $user->removeAvoir($commande->getLivre());
            
            
            $this->getUser()->setPoints($this->getUser()->getPoints() - $livre->getNombrePoints());
           
            
        }else {
            $this->getDoctrine()->getManager()->remove($commande);
        }
        $this->getDoctrine()->getManager()->flush();
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => 'redemande',
            'resultat'=>$resultat,
            'message' =>$alert,
           
            'source' =>'mesCommandes',
           
        ]);
    }
    
    /**
     * @Route("/Supcommand/{id}", name="app_SupprimerCom")
     */
    public function SupprimerCommande(Request $request, Commande $commande): Response
    {
       
        if($commande->getEnvoyeur()->getNewDemande()>0){
            $commande->getEnvoyeur()->setNewDemande($commande->getEnvoyeur()->getNewDemande() -1);
        }
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $commande->getCommandeur()->addPoints($commande->getLivre()->getNombrePoints());
        $commande->getLivre()->addProprietaire($commande->getEnvoyeur());
        $commande->setSupprimee(true);
        
      
        $this->getDoctrine()->getManager()->flush();
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => 'supprime',
             
           
            'source' =>'mesCommandes',
             
           
            
        ]);
    }

     /**
     * @Route("/mescommand", name="app_mesCom")
     */
    public function MesCommande(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => '',
        
            'source' =>'mesCommandes',  
              
        ]);
    }

     /**
     * @Route("/vacances", name="app_vacances")
     */
    public function AllerEnVacances(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $this->getUser()->setVacance(true);
        $this->getDoctrine()->getManager()->flush(); 
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => '',
        
            'source' =>'vacance',  
              
        ]);
    }

    /**
     * @Route("/vacancess", name="app_retourVacances")
     */
    public function RetourEnVacances(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $this->getUser()->setVacance(false);
        $this->getDoctrine()->getManager()->flush(); 
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => '',
        
            'source' =>'RetourVacance',  
              
        ]);
    }

     /**
     * @Route("/reclamation/{id}", name="reclamations")
     */
    public function Reclamations(Request $request, Commande $commande): Response
    {
        $resultat = 'réclamationacceptee';
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        if($commande->getSupprimee()){
           
        }else{
            $temps = $this->NombreDeJours($commande->getDateValidation());
            if($temps >= 10 ){
                $this->getUser()->addPoints($commande->getLivre()->getNombrePoints());
            }else{
                $resultat = 'réclamationrefusee';
            }
        }
        $this->getDoctrine()->getManager()->flush();   
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => $resultat,
            'temps'=>$temps, 
             'points'=>$commande->getLivre()->getNombrePoints(),
            'res'=> $this->NombreDeJours('06-01-2021'),
            'source' =>'mesDemandes',  
              
        ]);
    }

    function NombreDeJours($date1){
        
        return ceil(abs(strtotime($date1) - strtotime(date('d-m-Y'))) / 86400);
    }
     
    

     /**
     * @Route("/mesenvoies", name="app_mesEnvoies")
     */
    public function MesEnvoies(): Response
    {
        
        if( $this->getUser() != null){
            $this->getUser()->setNewEnvoie(0);
            $this->getDoctrine()->getManager()->flush();
        }
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => '',
           
            'source' =>'mesEnvoies',  
            
        ]);
    }

    /**
     * @Route("/mesdemandes", name="app_mesDemandes")
     */
    public function MesDemandess(): Response
    {
        if($this->getUser() != null){
            $this->getUser()->setNewDemande(0);
            $this->getDoctrine()->getManager()->flush();
        }
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            'livres' => '',
            
           
            'source' =>'mesDemandes',    
        ]);
    }

    /**
     * @Route("/empruntPoints", name="pret_points")
     */
    public function AccorderPoints(Request $request ): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        
        $this->getUser()->setPointsEmprunter($_POST['points']);
        $this->getUser()->addPoints($_POST['points']);
        $this->getDoctrine()->getManager()->flush();
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
             'points'=>$_POST['points'],
            'action'=>'pret',
           
            'source' =>'pret_points',    
        ]);
    }

     /**
     * @Route("/emprunter_points", name="emprunter_points")
     */
    public function EmprunterPoints(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $points = $this->getUser()->getPointsEmprunter();
        $alert = 'faux';
        $nombres = 0;
        if($points>0){
            $alert = "Vous n'avez pas totalement rembourser votre prêt de points. Il vous reste ". $points . " à rembourser.";
        }else{
                foreach ($this->getUser()->getEnvoies() as $com) {
                    if($com->getEnvoyer()){
                        $nombres += 1;
                    }
                }
            
                if($nombres<10){
                    if($nombres >1){
                        $li = $nombres ." livres.";
                    }else{$li = $nombres ." livre.";}
                    $alert ="Vous n'avez envoyer juste ". $li." Il faut envoyer au ninimum 10 livres pour pouvoir emprunter des points." ;
                }else{
                    $alert = 'true';
                }
        }

       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            
            'alert'=>$alert,

           
            'source' =>'emprunterpoints',    
        ]);
    }



    /**
     * @Route("/transfertpoints", name="transfert_points")
     */
    public function TransferererPoints(Request $request): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $alert = 'true';
        $points = $_POST['points'];
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByemail($_POST['email']);
        
       
        if($user){
           $user ->addPoints($points);
           $this->getUser()->setPoints($this->getUser()->getPoints() - $points);
           $this->getDoctrine()->getManager()->flush();
        }else{
            $user = new User();
               $alert="Aucun utilisateur ne correspond à cette l'email".$_POST['email'].".Recommancez en donnant un bon email.";
        }

       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            
            'alert'=>$alert,
            'user'=>$user,
            'points'=>$points,

           
            'source' =>'transfertpoints',    
        ]);
    }

    /**
     * @Route("/supprimerCompte", name="Supprimer_mon_compte")
     */
    public function SupprimerMonCompte(): Response
    {   
        foreach ($this->getUser()->getEnvoies() as $commande) {
            $this->getDoctrine()->getManager()->remove($commande);
            $this->getDoctrine()->getManager()->flush();
        }
        foreach ($this->getUser()->getCommandes() as $commande) {
            $this->getDoctrine()->getManager()->remove($commande);
            $this->getDoctrine()->getManager()->flush();
        }
        $this->getDoctrine()->getManager()->remove($this->getUser());
        $this->getDoctrine()->getManager()->flush();
       
        return new RedirectResponse('accueil');
    }

     /**
     * @Route("/guide", name="guide_utilisation")
     */
    public function GuideDutilisation(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            
            'action'=>'guide',
           
            'source' =>'guide_utilisation',    
        ]);
    }

     /**
     * @Route("/point", name="guide_points")
     */
    public function GuideDutilisationPoints(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            
            
            'action'=>'points',
            'source' =>'guide_utilisation',    
        ]);
    }

     /**
     * @Route("/guide_voyages", name="guide_voyages")
     */
    public function GuideDutilisationVoyage(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            
            
            'action'=>'voyages',
            'source' =>'guide_utilisation',    
        ]);
    }

    /**
     * @Route("/ajouter__livre", name="guide_ajoutlivre")
     */
    public function GuideDutilisationAjoutLivre(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            
            
            'action'=>'ajouterlivre',
            'source' =>'guide_utilisation',    
        ]);
    }

    /**
     * @Route("/echanger__livre", name="guide_echangerlivre")
     */
    public function GuideDutilisationEchangerLivre(): Response
    {
        
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
       
        return $this->render('accueil/index.html.twig', [
            
            'categories' =>$categories,
            
            
            'action'=>'echangerlivre',
            'source' =>'guide_utilisation',    
        ]);
    }
}
