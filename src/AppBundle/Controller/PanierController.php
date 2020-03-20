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
        $qualificationUser = $user->getQualification()->getId();

        $session = new Session();
        if ($session->has('panier'))
            $panier = $session->get('panier');

        // correction du panier en enlevant les produits à qté = 0
        $panierCorrige = [];

        foreach ($panier as $index => $addproduct) {
            if ($addproduct->getQuantity() != 0) {
                $panierCorrige[$index] = $addproduct;
            }
        }

        // liste des paquetages idpdt type par qualification
        $paquetageType = $paquetageQualification->getPaquetageType($user);
        $paquetageChaussures = [30, 31, 32,49,50,51,52];
        $arrayRules = $this->rulesQualificationPanier($user);


        $qte1 = 0;
        $msg1='ok';
        $qte2 = 0;
        $msg2='ok';
        $qte3 = 0;
        $msg3='ok';
        $qte4 = 0;
        $msg4='ok';
        $msg5='ok';
        $msg6='ok';
        $pantalonOrder=[];
        $teeshirtOrder=[];

        if ($qualificationUser == 1 || $qualificationUser == 2) {
            foreach ($panierCorrige as $index => $addpdt) {
                if (in_array($index, $arrayRules['pantalons'])) {
                    $qte1 = $qte1 + $addpdt->getQuantity();
                    $pantalonOrder[] = $index;
                }
                if (in_array($index, $arrayRules['chaussures'])) {
                    $qte2 = $qte2 + $addpdt->getQuantity();
                }
                if (in_array($index, $arrayRules['vestes'])) {
                    $qte3 = $qte3 + $addpdt->getQuantity();
                }
                if (in_array($index,$arrayRules['teeshirts'])) {
                    $teeshirtOrder[] = $index;
                }
            }
            if ($qte1 < 3) {
                $msg1 = 'ko';
            }
            if ($qte2 == 0) {
                $msg2 = 'ko';
            }
            if ($qte3 == 0) {
                $msg3 = 'ko';
            }
            if (count($pantalonOrder) < 2) {
                $msg5 = 'ko';
            }
            if (count($teeshirtOrder) < 2) {
                $msg6 = 'ko';
            }

        }

        if ($qualificationUser == 3 || $qualificationUser == 4) {
            foreach ($panierCorrige as $index => $addpdt) {
                if (in_array($index, $arrayRules['pantalons'])) {
                    $qte1 = $qte1 + $addpdt->getQuantity();
                    $pantalonOrder[] = $index;
                }
                if (in_array($index, $arrayRules['chaussures'])) {
                    $qte2 = $qte2 + $addpdt->getQuantity();
                }
                if (in_array($index, $arrayRules['vestes'])) {
                    $qte3 = $qte3 + $addpdt->getQuantity();
                }
                if (in_array($index, $arrayRules['shirts'])) {
                    $qte4 = $qte4 + $addpdt->getQuantity();
                }
                if (in_array($index,$arrayRules['teeshirts'])) {
                    $teeshirtOrder[] = $index;
                }
            }

            if ($qte1 < 3) {
                $msg1 = 'ko';
            }
            if ($qte2 == 0) {
                $msg2 = 'ko';
            }
            if ($qte3 == 0) {
                $msg3 = 'ko';
            }
            if ($qte4 < 3) {
                $msg4= 'ko';
            }
            if (count($pantalonOrder) < 2) {
                $msg5 = 'ko';
            }
            if (count($teeshirtOrder) < 2) {
                $msg6 = 'ko';
            }
        }
        if ($qualificationUser == 5) {
            foreach ($panierCorrige as $index => $addpdt) {
                if (in_array($index, $arrayRules['chaussures'])) {
                    $qte2 = $qte2 + $addpdt->getQuantity();
                }
                if (in_array($index, $arrayRules['pantalons'])) {
                    $qte1 = $qte1 + $addpdt->getQuantity();
                    $pantalonOrder[] = $index;
                }
                if (in_array($index,$arrayRules['teeshirts'])) {
                    $teeshirtOrder[] = $index;
                }
            }
            if ($qte1 < 3) {
                $msg1 = 'ko';
            }
            if ($qte2 == 0) {
                $msg2 = 'ko';
            }
            if (count($pantalonOrder) < 2) {
                $msg5 = 'ko';
            }
            if (count($teeshirtOrder) < 2) {
                $msg6 = 'ko';
            }

        }

            $pdtPanier = [];
            $idPdtPanier = [];
            $errorMessage = 'ok';
            $errorMessageQte = 'ok';
            $panierCorrigeChaussures = [];
            $idPdtPanierChaussures = [];
            $qtePanierChaussures = 0;


            // transformation du panier corrigé en tableau des idpdt hors chaussures
            // transformation du panier corrigé en tableau des addproduct hors chaussures
            // création du tableau des idpdt chaussures
            // création du tableau des qtés de chaussures
            foreach ($panierCorrige as $index => $addproduct) {
                $pdtPanier[$index] = $addproduct;
                $idPdtPanier[] = $index;
                if ($addproduct->getProduct()->getCategory()->getId() === 9 || $addproduct->getProduct()->getCategory()->getId() === 10 ||
                    $addproduct->getProduct()->getCategory()->getId() === 11) {
                    $panierCorrigeChaussures[$index] = $addproduct->getQuantity();
                    $idPdtPanierChaussures[] = $index;
                }
            }

            // on vérifie si tous les idpdt attendus dans le paquetage type sont présents
            // sinon on envoie un message d'erreur
            foreach ($paquetageType as $value) {
                if (!in_array($value, $idPdtPanier)) {
                    $errorMessage = 'ko';
                }
            }

            // on vérifie si des idpdt de chaussures sont bien présents dans le panier
            // sinon on envoie un message d'erreur
            if (count($idPdtPanierChaussures) == 0) {
                $errorMessage = 'ko';
            }

            // on vérifie si les qtés de chaussures dans le panier sont correctes
            foreach ($panierCorrigeChaussures as $value) {
                $qtePanierChaussures = $qtePanierChaussures + $value;
            }

            if ($qtePanierChaussures == 0 || $qtePanierChaussures > 2) {
                $errorMessageQte = 'ko';
            }

            // on vérifie si les qtés des produits dans le panier corrigé hors chaussures sont valides
            foreach ($pdtPanier as $addproduct) {
                $minQty = $addproduct->getProduct()->getMinQty();
                $maxQty = $addproduct->getProduct()->getMaxQty();
                $cartQty = $addproduct->getQuantity();
                if ($cartQty < $minQty || $cartQty > $maxQty) {
                    $errorMessageQte = 'ko';
                }
            }


            return $this->render('front/panier.html.twig', array(
                'panier' => $panierCorrige,
                'user' => $user,
                'msg1' => $msg1,
                'msg2' => $msg2,
                'msg3' => $msg3,
                'msg4' => $msg4,
                'msg5' => $msg5,
                'msg6' => $msg6,
            ));

    }

    private function rulesQualificationPanier($user)
    {

        $qualificationUser = $user->getQualification()->getId();
        $arrayRules = [];

        if ($qualificationUser == 1) {
            $arrayRules['pantalons'] = [6, 11];
            $arrayRules['chaussures'] = [30, 31, 32, 49, 50, 51, 52];
            $arrayRules['vestes'] = [16, 53];
            $arrayRules['teeshirts'] = [21, 24];
        } elseif ($qualificationUser == 2) {
            $arrayRules['pantalons'] = [7, 12];
            $arrayRules['chaussures'] = [30, 31, 32, 49, 50, 51, 52];
            $arrayRules['vestes'] = [17, 54];
            $arrayRules['teeshirts'] = [22, 25];
        } elseif ($qualificationUser == 3) {
            $arrayRules['pantalons'] = [8, 13];
            $arrayRules['chaussures'] = [30, 31, 32, 49, 50, 51, 52];
            $arrayRules['vestes'] = [18, 55];
            $arrayRules['shirts'] = [27, 45, 47];
            $arrayRules['teeshirts'] = [45, 47];
        } elseif ($qualificationUser == 4) {
            $arrayRules['pantalons'] = [9, 14];
            $arrayRules['chaussures'] = [30, 31, 32, 49, 50, 51, 52];
            $arrayRules['vestes'] = [19, 56];
            $arrayRules['shirts'] = [28, 46, 48];
            $arrayRules['teeshirts'] = [46, 48];
        } elseif ($qualificationUser == 5) {
            $arrayRules['pantalons'] = [10, 15];
            $arrayRules['chaussures'] = [30, 31, 32, 49, 50, 51, 52];
            $arrayRules['vestes'] = [20, 57];
            $arrayRules['teeshirts'] = [23, 26];
        }

        return $arrayRules;
    }
}
