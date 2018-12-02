<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


class PanierController extends Controller
{
    /**
     * @Route("/panier", name="panier")
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $session = new Session();
        if($session->has('panier'))
            $panier = $session->get('panier');

        $panierCorrige = [];

        foreach ($panier as $index=>$addproduct) {
            if ($addproduct->getQuantity() != 0) {
                $panierCorrige[$index]=$addproduct;
            }
        }

        return $this->render('front/panier.html.twig', array(
            'panier' => $panierCorrige,
            'user' => $user
        ));
    }
}
