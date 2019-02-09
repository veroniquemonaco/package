<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Addproduct;
use AppBundle\Entity\Commande;
use AppBundle\Entity\Product;
use AppBundle\Entity\Taille;
use AppBundle\Entity\User;
use AppBundle\Repository\AddproductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Service\YearPaquetageService;

class PackageController extends Controller
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueilAction(YearPaquetageService $yearPaquetageService)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $userQualification = $user->getQualification()->getId();


        if ($userQualification === 7 ) {
            return $this->redirectToRoute('admin');
        } else {
//            $yearPaquetage = '';
//            $yearPaquetageOld = '';
//            $date = new \DateTime();
//            $dateMonth = $date->format('m');
//            $year = $date->format('Y');
//            if (in_array($dateMonth,[7,8,9,10,11,12])){
//                $yearPaquetage = intval($year) + 1;
//                $yearPaquetageOld = intval($year);
//            } else if (in_array($dateMonth,[1,2,3,4,5,6])){
//                $yearPaquetage = intval($year);
//                $yearPaquetageOld = intval($year)-1;
//            }
            $yearPaquetage = '';
            $yearPaquetageOld = '';
            $years = $yearPaquetageService->getYearPaquetage();
            $yearPaquetage = $years[0];
            $yearPaquetageOld = $years[1];


            $commandeUser = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetage,
                'user' => $user));

            $commandeUserOld = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetageOld,
                'user' => $user));

            if ($commandeUserOld != []) {
                $commandeYearPaquetageOld = $commandeUserOld[0]->getCommande();
            }
            if ($commandeUser != []) {
                $commandeYearPaquetage = $commandeUser[0]->getCommande();
            }

            return $this->render('front/accueil.html.twig', array(
                'user' => $user,
                'commandeYear' => $commandeUser,
                'commandeYearOld' => $commandeUserOld,
                'yearPaquetage' => $yearPaquetage,
                'yearPaquetageOld' => $yearPaquetageOld
            ));
        }
    }

    /**
     * @Route("/package", name="package")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $yearPaquetage = '';
        $date = new \DateTime();
        $dateMonth = $date->format('m');
        $year = $date->format('Y');
        if (in_array($dateMonth,[7,8,9,10,11,12])){
            $yearPaquetage = intval($year) + 1;
            $yearPaquetageOld = intval($year);
        } else if (in_array($dateMonth,[1,2,3,4,5,6])){
            $yearPaquetage = intval($year);
            $yearPaquetageOld = intval($year)-1;
        }

        $commandeYearPaquetage = [];
        $commandeYearPaquetageOld = [];
        $backOrder = [];
        $backOrderOld = [];
        $callpanier = [];
        $callpanieractif = [];
        $panier = [];

        $qualificationId = $this->getUser()->getQualification()->getId();

        $produits = $em->getRepository('AppBundle:Product')->searchBy($qualificationId);

        $session = new Session();

        $commandeUser = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetage,
            'user' => $user));

        $commandeUserOld = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetageOld,
            'user' => $user));

        if ($commandeUserOld != []) {
            $commandeYearPaquetageOld = $commandeUserOld[0]->getCommande();

            foreach ($commandeYearPaquetageOld as $idpdt => $orderarray) {
                $backOrderOld[$idpdt] = $idpdt;
            }
        }


        if ($commandeUser != []) {
            $commandeYearPaquetage = $commandeUser[0]->getCommande();


            foreach ($commandeYearPaquetage as $idpdt => $orderarray) {
                $backOrder[$idpdt] = $idpdt;
            }


            foreach ($commandeYearPaquetage as $idpdt => $orderarray) {
                $addProductCde = new Addproduct();
                $addProductCde->setProduct(
                    $em->getRepository(Product::class)->findOneBy(['id' => $orderarray['idpdt']]));
                $addProductCde->setTaille(
                    $em->getRepository(Taille::class)->findOneBy(['id' => $orderarray['tailleid']])
                );
                $addProductCde->setQuantity($orderarray['qte']);
                $addProductCde->setPrice(
                    $em->getRepository(Product::class)->findOneBy(['id' => $orderarray['idpdt']])->getPrix()
                );
                $em->persist($addProductCde);
                $em->flush();
                $callpanier[$idpdt] = $addProductCde;
            }

            $callpanieractif=[];
            foreach ($callpanier as $idpdt=>$addProductCde) {
                $actif = $em->getRepository(Product::class)->findOneBy(['id' => $idpdt])->isActif();
                if ($actif) {
                    $callpanieractif[$idpdt] = $addProductCde;
                }
            }

        }


        if (!$session->has('panier')) $session->set('panier', $callpanieractif);
        $panier = $session->get('panier');
        dump($panier);
        $amountCart=0;
        foreach($panier as $index=>$addpdt) {
            $amountCart = $amountCart + $addpdt->getPrice()*$addpdt->getQuantity();
        }
        dump($amountCart);

        if ($request->isXmlHttpRequest()) {
            if (!$session->has('panier')) $session->set('panier', $callpanier);
            $panier = $session->get('panier');

            $amountCartAjax = $amountCart;

            $data = $request->get('addProduct');
            $lessData = $request->get('lessProduct');
            $lessDataPanier = $request->get('lessProductPanier');

            if ($data) {
                $qty = intval($data['qty']);
                $product = $em->getRepository(Product::class)
                    ->findOneBy(['id' => $data['idPdt']]);
                $productId = $product->getId();
                $price = $product->getPrix();
                $amount = $price * $qty;
                $amountCartAjax = $amountCartAjax + $amount;
                $taille = $em->getRepository(Taille::class)->findOneBy(['id' => $data['taille']]);
                $addProduct = new Addproduct();
                $addProduct->setProduct($product);
                $addProduct->setTaille($taille);
                $addProduct->setQuantity($qty);
                $addProduct->setPrice($price);
                $em->persist($addProduct);
                $em->flush();

//                $panier[$addProduct->getProduct()->getId()] = $addProduct;
                $panier[$productId] = $addProduct;
                $session->set('panier', $panier);
                array_push($backOrder, $productId);


                return new JsonResponse(array("addPdtId" => json_encode($addProduct->getProduct()->getId()),
                    "addPdtTaille" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtQty" => json_encode($addProduct->getQuantity()),
                    "addPdtLibelle" => json_encode($addProduct->getProduct()->getName(),JSON_UNESCAPED_UNICODE),
                    "addPdtTailleLibelle" => json_encode($addProduct->getTaille()->getName()),
                    "addPdtTailleId" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtPrix" => json_encode($addProduct->getPrice()),
                    "amountCartAjax" => $amountCartAjax,
                ));
            } elseif ($lessData) {
                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessData['idPdt']]);
                $amountCartAjax = $amountCartAjax - ($panier[$product->getId()]->getquantity()*$panier[$product->getId()]->getPrice());
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtId" => json_encode($product->getId()),
                    "amountCartAjax" => $amountCartAjax
                ));
            } elseif ($lessDataPanier) {
                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessDataPanier['idPdt']]);
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtIdPanier" => json_encode($product->getId()),
                ));
            }
        }

        return $this->render('front/package.html.twig', array(
            'produits' => $produits,
            'user' => $user,
            'commandeYearPaquetage' => $commandeYearPaquetage,
            'backOrder' => $backOrder,
            'yearPaquetage' => $yearPaquetage,
            'panier' => $panier,
        ));
    }

    /**
     * @Route("/packageYearBefore", name="package_year_before")
     */
    public function packageYearBefore(Request $request,YearPaquetageService $yearPaquetageService)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

//        $yearPaquetage = '';
//        $date = new \DateTime();
//        $dateMonth = $date->format('m');
//        $year = $date->format('Y');
//        if (in_array($dateMonth,[7,8,9,10,11,12])){
//            $yearPaquetage = intval($year) + 1;
//            $yearPaquetageOld = intval($year);
//        } else if (in_array($dateMonth,[1,2,3,4,5,6])){
//            $yearPaquetage = intval($year);
//            $yearPaquetageOld = intval($year)-1;
//        }

        $yearPaquetage = '';
        $yearPaquetageOld = '';
        $years = $yearPaquetageService->getYearPaquetage();
        $yearPaquetage = $years[0];
        $yearPaquetageOld = $years[1];

        $commandeYearPaquetage = [];
        $commandeYearPaquetageOld = [];
        $backOrder = [];
        $backOrderOld = [];
        $callpanier = [];
        $panier = [];

        $qualificationId = $this->getUser()->getQualification()->getId();

        $produits = $em->getRepository('AppBundle:Product')->searchBy($qualificationId);

        $session = new Session();

        $commandeUser = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetage,
            'user' => $user));

        $commandeUserOld = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetageOld,
            'user' => $user));

        if ($commandeUserOld != []) {
            $commandeYearPaquetageOld = $commandeUserOld[0]->getCommande();

            foreach ($commandeYearPaquetageOld as $idpdt => $orderarray) {
                $backOrderOld[$idpdt] = $idpdt;
            }

            foreach ($commandeYearPaquetageOld as $idpdt => $orderarray) {
                $addProductCde = new Addproduct();
                $addProductCde->setProduct(
                    $em->getRepository(Product::class)->findOneBy(['id' => $orderarray['idpdt']]));
                $addProductCde->setTaille(
                    $em->getRepository(Taille::class)->findOneBy(['id' => $orderarray['tailleid']])
                );
                $addProductCde->setQuantity($orderarray['qte']);
                $addProductCde->setPrice(
                    $em->getRepository(Product::class)->findOneBy(['id' => $orderarray['idpdt']])->getPrix()
                );
                $em->persist($addProductCde);
                $em->flush();
                $callpanier[$idpdt] = $addProductCde;
            }

            $callpanieractif=[];
            foreach ($callpanier as $idpdt=>$addProductCde) {
                $actif = $em->getRepository(Product::class)->findOneBy(['id' => $idpdt])->isActif();
                if ($actif) {
                    $callpanieractif[$idpdt] = $addProductCde;
                }
            }
        }

        if (!$session->has('panier')) $session->set('panier', $callpanieractif);
        $panier = $session->get('panier');
        $amountCart=0;
        foreach($panier as $index=>$addpdt) {
            $amountCart = $amountCart + $addpdt->getPrice()*$addpdt->getQuantity();
        }
        dump($panier);

        if ($request->isXmlHttpRequest()) {
            if (!$session->has('panier')) $session->set('panier', $callpanier);
            $panier = $session->get('panier');
            $data = $request->get('addProduct');
            $lessData = $request->get('lessProduct');
            $lessDataPanier = $request->get('lessProductPanier');

            if ($data) {
                $qty = intval($data['qty']);
                $product = $em->getRepository(Product::class)
                    ->findOneBy(['id' => $data['idPdt']]);
                $productId = $product->getId();
                $price = $product->getPrix();
                $amount = $price * $qty;
                $amountCartAjax = $amountCart + $amount;
                $taille = $em->getRepository(Taille::class)->findOneBy(['id' => $data['taille']]);
                $addProduct = new Addproduct();
                $addProduct->setProduct($product);
                $addProduct->setTaille($taille);
                $addProduct->setQuantity($qty);
                $addProduct->setPrice($price);
                $em->persist($addProduct);
                $em->flush();

                $panier[$productId] = $addProduct;
                $session->set('panier', $panier);
                array_push($backOrder, $productId);

                return new JsonResponse(array("addPdtId" => json_encode($addProduct->getProduct()->getId()),
                    "addPdtTaille" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtQty" => json_encode($addProduct->getQuantity()),
                    "addPdtLibelle" => json_encode($addProduct->getProduct()->getName()),
                    "addPdtTailleLibelle" => json_encode($addProduct->getTaille()->getName()),
                    "addPdtTailleId" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtPrix" => json_encode($addProduct->getPrice()),
                ));
            } elseif ($lessData) {

                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessData['idPdt']]);
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtId" => json_encode($product->getId())
                ));
            } elseif ($lessDataPanier) {
                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessDataPanier['idPdt']]);
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtIdPanier" => json_encode($product->getId())
                ));
            }
        }

        return $this->render('front/package.html.twig', array(
            'produits' => $produits,
            'user' => $user,
            'commandeYearPaquetage' => $commandeYearPaquetageOld,
            'backOrder' => $backOrderOld,
            'yearPaquetage' => $yearPaquetage,
            'panier' => $panier,
            'amountCart' => $amountCart
        ));
    }

    /**
     * @Route("/packageReturn", name="package_return")
     */
    public function packageReturn(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $yearPaquetage = '';
        $date = new \DateTime();
        $dateMonth = $date->format('m');
        $year = $date->format('Y');
        if (in_array($dateMonth,[7,8,9,10,11,12])){
            $yearPaquetage = intval($year) + 1;
            $yearPaquetageOld = intval($year);
        } else if (in_array($dateMonth,[1,2,3,4,5,6])){
            $yearPaquetage = intval($year);
            $yearPaquetageOld = intval($year)-1;
        }
        $commandeYearPaquetage = [];
        $commandeYearPaquetageOld = [];
        $backOrder = [];
        $backOrderOld = [];
        $callpanier = [];
        $panier = [];

        $qualificationId = $this->getUser()->getQualification()->getId();

        $produits = $em->getRepository('AppBundle:Product')->searchBy($qualificationId);

        $session = new Session();

//        $commandeUser = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetage,
//            'user' => $user));
//
//        $commandeUserOld = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetageOld,
//            'user' => $user));
//
//        if ($commandeUserOld != []) {
//            $commandeYearPaquetageOld = $commandeUserOld[0]->getCommande();
//
//            foreach ($commandeYearPaquetageOld as $idpdt => $orderarray) {
//                $backOrderOld[$idpdt] = $idpdt;
//            }
//
//            foreach ($commandeYearPaquetageOld as $idpdt => $orderarray) {
//                $addProductCde = new Addproduct();
//                $addProductCde->setProduct(
//                    $em->getRepository(Product::class)->findOneBy(['id' => $orderarray['idpdt']]));
//                $addProductCde->setTaille(
//                    $em->getRepository(Taille::class)->findOneBy(['id' => $orderarray['tailleid']])
//                );
//                $addProductCde->setQuantity($orderarray['qte']);
//                $addProductCde->setPrice(
//                    $em->getRepository(Product::class)->findOneBy(['id' => $orderarray['idpdt']])->getPrix()
//                );
//                $em->persist($addProductCde);
//                $em->flush();
//                $callpanier[$idpdt] = $addProductCde;
//            }
//
//
//        }

//        if (!$session->has('panier')) $session->set('panier', $callpanier);
        $panier = $session->get('panier');
        foreach ($panier as $index=>$addproduct) {
            $backOrderOld[$index] = $index;
        }

        if ($request->isXmlHttpRequest()) {
            if (!$session->has('panier')) $session->set('panier', $callpanier);
            $panier = $session->get('panier');

            $data = $request->get('addProduct');
            $lessData = $request->get('lessProduct');
            $lessDataPanier = $request->get('lessProductPanier');

            if ($data) {
                $qty = intval($data['qty']);
                $product = $em->getRepository(Product::class)
                    ->findOneBy(['id' => $data['idPdt']]);
                $productId = $product->getId();
                $price = $product->getPrix();
                $amount = $price * $qty;
                $taille = $em->getRepository(Taille::class)->findOneBy(['id' => $data['taille']]);
                $addProduct = new Addproduct();
                $addProduct->setProduct($product);
                $addProduct->setTaille($taille);
                $addProduct->setQuantity($qty);
                $addProduct->setPrice($price);
                $em->persist($addProduct);
                $em->flush();

                $panier[$productId] = $addProduct;
                $session->set('panier', $panier);
                array_push($backOrder, $productId);


                return new JsonResponse(array("addPdtId" => json_encode($addProduct->getProduct()->getId()),
                    "addPdtTaille" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtQty" => json_encode($addProduct->getQuantity()),
                    "addPdtLibelle" => json_encode($addProduct->getProduct()->getName()),
                    "addPdtTailleLibelle" => json_encode($addProduct->getTaille()->getName()),
                    "addPdtTailleId" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtPrix" => json_encode($addProduct->getPrice()),
                ));
            } elseif ($lessData) {

                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessData['idPdt']]);
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtId" => json_encode($product->getId())
                ));
            } elseif ($lessDataPanier) {
                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessDataPanier['idPdt']]);
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtIdPanier" => json_encode($product->getId())
                ));
            }
        }

        return $this->render('front/package.html.twig', array(
            'produits' => $produits,
            'user' => $user,
            'commandeYearPaquetage' => $commandeYearPaquetageOld,
            'backOrder' => $backOrderOld,
            'yearPaquetage' => $yearPaquetage,
            'panier' => $panier,
        ));
    }


    /**
     * @Route("/packageInit", name="package_init")
     */
    public function packageInit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $yearPaquetage = '';
        $date = new \DateTime();
        $dateMonth = $date->format('m');
        $year = $date->format('Y');
        if (in_array($dateMonth,[7,8,9,10,11,12])){
            $yearPaquetage = intval($year) + 1;
            $yearPaquetageOld = intval($year);
        } else if (in_array($dateMonth,[1,2,3,4,5,6])){
            $yearPaquetage = intval($year);
            $yearPaquetageOld = intval($year)-1;
        }
        $commandeYearPaquetage = [];
        $commandeYearPaquetageOld = [];
        $backOrder = [];
        $backOrderOld = [];
        $callpanier = [];
        $panier = [];

        $qualificationId = $this->getUser()->getQualification()->getId();

        $produits = $em->getRepository('AppBundle:Product')->searchBy($qualificationId);

        $session = new Session();

        if (!$session->has('panier')) $session->set('panier', $callpanier);
        $panier = $session->get('panier');

        if ($request->isXmlHttpRequest()) {
            if (!$session->has('panier')) $session->set('panier', $callpanier);
            $panier = $session->get('panier');

            $data = $request->get('addProduct');
            $lessData = $request->get('lessProduct');
            $lessDataPanier = $request->get('lessProductPanier');

            if ($data) {
                $qty = intval($data['qty']);
                $product = $em->getRepository(Product::class)
                    ->findOneBy(['id' => $data['idPdt']]);
                $productId = $product->getId();
                $price = $product->getPrix();
                $amount = $price * $qty;
                $taille = $em->getRepository(Taille::class)->findOneBy(['id' => $data['taille']]);
                $addProduct = new Addproduct();
                $addProduct->setProduct($product);
                $addProduct->setTaille($taille);
                $addProduct->setQuantity($qty);
                $addProduct->setPrice($price);
                $em->persist($addProduct);
                $em->flush();

                $panier[$productId] = $addProduct;
                $session->set('panier', $panier);
                array_push($backOrder, $productId);


                return new JsonResponse(array("addPdtId" => json_encode($addProduct->getProduct()->getId()),
                    "addPdtTaille" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtQty" => json_encode($addProduct->getQuantity()),
                    "addPdtLibelle" => json_encode($addProduct->getProduct()->getName()),
                    "addPdtTailleLibelle" => json_encode($addProduct->getTaille()->getName()),
                    "addPdtTailleId" => json_encode($addProduct->getTaille()->getId()),
                    "addPdtPrix" => json_encode($addProduct->getPrice()),
                ));
            } elseif ($lessData) {

                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessData['idPdt']]);
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtId" => json_encode($product->getId())
                ));
            } elseif ($lessDataPanier) {
                $product = $em->getRepository(Product::class)->findOneBy(['id' => $lessDataPanier['idPdt']]);
                unset($panier[$product->getId()]);
                $session->set('panier', $panier);
                unset($backOrder[$product->getId()]);
                return new JsonResponse(array(
                    "lessPdtIdPanier" => json_encode($product->getId())
                ));
            }
        }

        return $this->render('front/package.html.twig', array(
            'produits' => $produits,
            'user' => $user,
            'commandeYearPaquetage' => $commandeYearPaquetageOld,
            'backOrder' => $backOrderOld,
            'yearPaquetage' => $yearPaquetage,
            'panier' => $panier,
        ));
    }

    /**
     * @Route("/reset/panier", name="reset_panier")
     *
     */
    public function ResetPanierAction(Session $session)
    {

        $session = new Session();
        if ($session->has('panier'))
            $session->remove('panier');

        return $this->redirectToRoute('package_init');


    }


}