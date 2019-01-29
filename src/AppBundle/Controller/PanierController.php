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

        $pdts = $paquetageQualification->getPaquetageType($user);

        dump($pdts);

        // liste des paquetages idpdt type par qualification
        $paquetageCompagnon = [6,11,16,21,24,33];
        $paquetageCompagnonMinorange = [7,12,17,22,24,33];
        $paquetageChefEquipe = [6,11,16,21,24,33];
        $paquetageChefEquipeMinorange = [6,11,16,21,24,33];
        $paquetageMaitrise = [6,11,16,21,24,33];
        $paquetageMaitriseMinorange = [6,11,16,21,24,33];

        $paquetageChaussures = [30,31,32];

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
        foreach ($paquetageCompagnon as $value) {
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



        return $this->render('front/panier.html.twig', array(
            'panier' => $panierCorrige,
            'user' => $user
        ));
    }
}
