<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Addproduct;
use AppBundle\Entity\ProductPackage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends Controller
{
    /**
     * @Route("/commande", name="commande")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $date = new \DateTime();
        $year = $date->format('Y');
        $yearPaquetage = intval($year) + 1;
        $commandeUser = [];
        $commande = [];
        $productsPackage = [];

        $session = new Session();
        if($session->has('panier'))
            $panier = $session->get('panier');

        $array = [];

        $productsPackage = $em->getRepository(ProductPackage::class)
            ->findBy(['user' => $user, 'yearPaquetage' => $yearPaquetage]);

        foreach ($productsPackage as $productPackage) {
            $em->remove($productPackage);
            $em->flush();
        }

        foreach ($panier as $index=>$addproduct) {
            $array[$index]['idpdt'] = $addproduct->getProduct()->getId();
            $array[$index]['libelle'] = $addproduct->getProduct()->getName();
            $array[$index]['qte'] = $addproduct->getQuantity();
            $array[$index]['taille'] = $addproduct->getTaille()->getName();
            $array[$index]['tailleid'] = $addproduct->getTaille()->getId();
            $array[$index]['prix'] = $addproduct->getPrice();
            $orderline = new ProductPackage();
            $idpdtUnique = $addproduct->getProduct()->getId().$addproduct->getTaille()->getName();
            $orderline->setUser($user);
            $orderline->setIdpdt($addproduct->getProduct()->getId());
            $orderline->setLibellePdt($addproduct->getProduct()->getName());
            $orderline->setTaille($addproduct->getTaille()->getName());
            $orderline->setTailleId($addproduct->getTaille()->getId());
            $orderline->setQty($addproduct->getQuantity());
            $orderline->setYearPaquetage($yearPaquetage);
            $orderline->setIdpdtUnique($idpdtUnique);
            $em->persist($orderline);
            $em->flush();
        }

        $commandeUser = $em->getRepository(Commande::class)->findBy(array('yearPaquetage'=>$yearPaquetage,
            'user'=>$user));
        if($commandeUser){
            $commandeUser = $commandeUser[0];
        }

        if (!$commandeUser) {
            $commande = new Commande();
            $commande->setDate(new \DateTime());
            $commande->setUser($this->getUser());
            $commande->setYearPaquetage($yearPaquetage);
            $commande->setValider(1);
            $commande->setCommande($array);
            $commande->setReference(4);
            $em->persist($commande);
            $em->flush();
        } else {
            $commandeUser->setDate(new \DateTime());
            $commandeUser->setValider(1);
            $commandeUser->setCommande($array);
            $commandeUser->setReference(4);
            $em->persist($commandeUser);
            $em->flush();
        }



        return $this->render('front/commande.html.twig', array(
            'commande' => $commande,
            'commandeUser' => $commandeUser,
            'user' => $user));

    }
}
