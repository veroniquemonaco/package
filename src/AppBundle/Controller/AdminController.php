<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agence;
use AppBundle\Entity\Commande;
use AppBundle\Entity\ProductPackage;
use AppBundle\Entity\User;
use AppBundle\Form\ExportCommandesType;
use AppBundle\Form\ExportUserCommandesType;
use AppBundle\Form\ExportAllCommandesType;
use AppBundle\Form\UserCreationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Valid;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function IndexAdminAction()
    {
        return $this->render('layout_admin.html.twig');
    }

    /**
     * @Route("/admin/exports", name="export_commandes")
     */
    public function ExportCommandesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $commandes = $em->getRepository(Commande::class)->findAll();
        $allOrderProducts = $em->getRepository(ProductPackage::class)->findAll();
        $users = $em->getRepository(User::class)->findAll();
        $commandesSearch = '';
        $array = [];
        $agence='';

        $form = $this->createForm(ExportCommandesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $agence = $data['agence']->getName();

            $commandesSearch = $em->getRepository(Commande::class)->searchBy($agence);

            $orderproductsSearch = $em->getRepository(ProductPackage::class)->searchOrderLineBy($agence);

            $tab = [];
            $array = [];
            foreach ($orderproductsSearch as $orderline) {
                $idpdtunique = $orderline->getIdpdtUnique();
                $qty = $orderline->getQty();
                $array[$idpdtunique]['libelle'] = $orderline->getLibellePdt();
                $array[$idpdtunique]['taille'] = $orderline->getTaille();
                if (!array_key_exists($idpdtunique, $tab)) {
                    $tab[$idpdtunique] = $qty;
                    $array[$idpdtunique]['qty'] = $tab[$idpdtunique];
                } else {
                    $tab[$idpdtunique] = $tab[$idpdtunique] + $qty;
                    $array[$idpdtunique]['qty'] = $tab[$idpdtunique];
                }
            }
        }
        
        $form2 = $this->createForm(ExportAllCommandesType::class);
        $form2->handleRequest($request);
        
        if($form2->isSubmitted() && $form2->isValid()) {
            $tab = [];
            $array = [];
            foreach ($allOrderProducts as $orderline) {
                $idpdtunique = $orderline->getIdpdtUnique();
                $qty = $orderline->getQty();
                $array[$idpdtunique]['libelle'] = $orderline->getLibellePdt();
                $array[$idpdtunique]['taille'] = $orderline->getTaille();
                if (!array_key_exists($idpdtunique, $tab)) {
                    $tab[$idpdtunique] = $qty;
                    $array[$idpdtunique]['qty'] = $tab[$idpdtunique];
                } else {
                    $tab[$idpdtunique] = $tab[$idpdtunique] + $qty;
                    $array[$idpdtunique]['qty'] = $tab[$idpdtunique];
                }
            }
        }

        $form3 = $this->createForm(ExportUserCommandesType::class);

        return $this->render('admin/exports.html.twig', array(
            'commandes' => $commandes,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'form3' => $form3->createView(),
            'users' => $users,
            'commandesSearch' => $commandesSearch,
            'syntheseCommande' => $array,
            'agence' => $agence,
        ));
    }


    /**
     * @Route("/admin/createuser", name="create_user")
     */
    public function createEmployeAction(UserPasswordEncoderInterface $encoder, Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserCreationType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $username = $user->getFirstname() . $user->getLastname();
            $email = $username . '@test.fr';
            $plainPassword = 'motdepasse';
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded)
                ->setRoles(['ROLE_USER']);
            $user->setUsername($username);
            $user->setEmail($email);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('index_user');

        }

        return $this->render('admin/createuser.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("admin/users/index", name="index_user")
     */
    public function usersIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/usersindex.html.twig', array(
            'users' => $users
        ));
    }

}
