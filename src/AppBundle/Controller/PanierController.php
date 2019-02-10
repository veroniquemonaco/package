<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Service\PaquetageQualification;


class PanierController extends Controller
{
    /**
     * @Route("/panier", name="panier")
     */
    public function indexAction(PaquetageQualification $paquetageQualification)
    {
        $user = $this->getUser();

        $session = new Session();
        if($session->has('panier'))
            $panier = $session->get('panier');

        // correction du panier en enlevant les produits à qté = 0
        $panierCorrige = [];

        foreach ($panier as $index=>$addproduct) {
            if ($addproduct->getQuantity() != 0) {
                $panierCorrige[$index]=$addproduct;
            }
        }
        // liste des paquetages idpdt type par qualification
        $paquetageType = $paquetageQualification->getPaquetageType($user);
        $paquetageChaussures = [30,31,32];
        $arrayRules = $this->rulesQualificationPanier($user);

        dump($paquetageType);
        dump($panierCorrige);
        dump($arrayRules);

        $pdtPanier = [];
        $idPdtPanier = [];
        $errorMessage = 'ok';
        $errorMessageQte = 'ok';
        $panierCorrigeChaussures= [];
        $idPdtPanierChaussures= [];
        $qtePanierChaussures = 0;


        // transformation du panier corrigé en tableau des idpdt hors chaussures
        // transformation du panier corrigé en tableau des addproduct hors chaussures
        // création du tableau des idpdt chaussures
        // création du tableau des qtés de chaussures
        foreach ($panierCorrige as $index=>$addproduct) {
            $pdtPanier[$index] = $addproduct;
            $idPdtPanier[] = $index;
            if ($addproduct->getProduct()->getCategory()->getId() === 9 || $addproduct->getProduct()->getCategory()->getId() === 10 ||
                $addproduct->getProduct()->getCategory()->getId() === 11 ) {
                $panierCorrigeChaussures[$index] = $addproduct->getQuantity();
                $idPdtPanierChaussures[]=$index;
            }
        }

        // on vérifie si tous les idpdt attendus dans le paquetage type sont présents
        // sinon on envoie un message d'erreur
        foreach ($paquetageType as $value) {
            if (!in_array($value,$idPdtPanier)) {
                $errorMessage='ko';
            }
        }

        // on vérifie si des idpdt de chaussures sont bien présents dans le panier
        // sinon on envoie un message d'erreur
        if (count($idPdtPanierChaussures) == 0) {
            $errorMessage='ko';
        }

        // on vérifie si les qtés de chaussures dans le panier sont correctes
        foreach ($panierCorrigeChaussures as $value) {
            $qtePanierChaussures = $qtePanierChaussures + $value;
        }

        if ($qtePanierChaussures == 0 || $qtePanierChaussures>2) {
            $errorMessageQte = 'ko';
        }

        // on vérifie si les qtés des produits dans le panier corrigé hors chaussures sont valides
        foreach ($pdtPanier as $addproduct) {
            $minQty =$addproduct->getProduct()->getMinQty();
            $maxQty =$addproduct->getProduct()->getMaxQty();
            $cartQty = $addproduct->getQuantity();
            if ($cartQty < $minQty || $cartQty > $maxQty) {
                $errorMessageQte = 'ko';
            }
        }

        dump($errorMessage);
        dump($errorMessageQte);

        return $this->render('front/panier.html.twig', array(
            'panier' => $panierCorrige,
            'user' => $user,
            'errorMessage' => $errorMessage,
            'errorMessageQte' => $errorMessageQte
        ));
    }

    private function rulesQualificationPanier($user) {

        $qualificationUser = $user->getQualification()->getId();
        $arrayRules = [];

        if ($qualificationUser == 1) {
           $arrayRules['pantalons']=[6,11];
           $arrayRules['chaussures']=[30,31,32];
           $arrayRules['vestes']=[16,33];
        } elseif ($qualificationUser == 2) {
            $arrayRules['pantalons']=[7,12];
            $arrayRules['chaussures']=[30,31,32];
            $arrayRules['vestes']=[17,34];
        } elseif ($qualificationUser == 3) {
            $arrayRules['pantalons'] = [8, 13];
            $arrayRules['chaussures'] = [30, 31, 32];
            $arrayRules['vestes'] = [18, 35];
            $arrayRules['shirts'] = [7, 45, 47];
        } elseif ($qualificationUser == 4) {
            $arrayRules['pantalons'] = [9, 14];
            $arrayRules['chaussures'] = [30, 31, 32];
            $arrayRules['vestes'] = [19, 36];
            $arrayRules['shirts'] = [8, 46, 48];
        }

        return $arrayRules;
    }
}
