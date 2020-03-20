<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agence;
use AppBundle\Entity\Commande;
use AppBundle\Entity\Category;
use AppBundle\Entity\Taille;
use AppBundle\Entity\UserOrderByCategory;
use AppBundle\Entity\ProductPackage;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\TailleRepository;
use AppBundle\Form\ExportCommandesType;
use AppBundle\Form\ExportUserCommandesType;
use AppBundle\Form\ExportAllCommandesType;
use AppBundle\Form\ExportSyntheseOrderUserType;
use AppBundle\Form\ExportSyntheseOrderCategoryType;
use AppBundle\Form\UserCreationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        $commandes2018 = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => 2019));

        $agenceOrleans = $em->getRepository(Agence::class)->find(4);
        $commandesOrleans2019 = $em->getRepository(Commande::class)->searchByAgenceYear(4,2019);

        $allOrderProducts = $em->getRepository(ProductPackage::class)->findAll();
        $users = $em->getRepository(User::class)->findAll();
        $commandesSearch = '';
        $commandesUser = '';
        $array = [];
        $agence = null;
        $paquetageType = null;
        $searchform = 'init';
        $userSearchId = '';
        $userYearPaquetage = '';
        $yearPaquetage = '';
        $yearPaquetage2 = '';


        $form = $this->createForm(ExportCommandesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$data['agence'] == null) {
                $agence = $data['agence']->getName();
            } else {
                $agence = null;
            }

            if (!$data['qualification'] == null) {
                $paquetageType = $data['qualification']->getName();
            } else {
                $paquetageType = null;
            }

            $commandesSearch = $em->getRepository(Commande::class)->searchBy($agence);

            $orderproductsSearch = $em->getRepository(ProductPackage::class)->searchOrderLineBy($agence, $paquetageType);

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

        if ($form2->isSubmitted() && $form2->isValid()) {
            $tab = [];
            $array = [];
            $searchform = 'all';
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
        $form3->handleRequest($request);

        if ($form3->isSubmitted() && $form3->isValid()) {
            $data = $form3->getData();
            $userSearchId=$data['recherche'];
            $userYearPaquetage=$data['yearOrder'];
            $commandesUser = $em->getRepository(Commande::class)->findBy(array('user' => $userSearchId,
                'yearPaquetage' => $userYearPaquetage));
        }

        $form4 = $this->createForm(ExportSyntheseOrderUserType::class);
        $form4->handleRequest($request);

        if ($form4->isSubmitted() && $form4->isValid()) {
            $data = $form4->getData();
            $yearPaquetage = $data['yearPaquetage'];
        }

        $form5 = $this->createForm(ExportSyntheseOrderCategoryType::class);
        $form5->handleRequest($request);

        if ($form5->isSubmitted() && $form5->isValid()) {
            $data = $form5->getData();
            $yearPaquetage2 = $data['yearPaquetage2'];
        }

        return $this->render('admin/exports.html.twig', array(
            'commandes' => $commandes,
            'commandes2018' => $commandes2018,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'form3' => $form3->createView(),
            'form4' => $form4->createView(),
            'form5' => $form5->createView(),
            'users' => $users,
            'commandesSearch' => $commandesSearch,
            'syntheseCommande' => $array,
            'agence' => $agence,
            'paquetageType' => $paquetageType,
            'searchform' => $searchform,
            'commandesUser' => $commandesUser,
            'userSearchId' => $userSearchId,
            'userYearPaquetage' => $userYearPaquetage,
            'yearPaquetage' => $yearPaquetage,
            'yearPaquetage2' => $yearPaquetage2,
            'commandes2019Orleans' => $commandesOrleans2019
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
            $username = $user->getFirstname() .', '. $user->getLastname();
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

    /**
     * @Route("/exports/ajax/{input}", name="exports_autocompletion")
     * @Method("POST")
     *
     * @param Request $request
     * @param $input
     *
     * @return JsonResponse
     */
    public function autocompleteAction(Request $request, $input = null)
    {
        if ($request->isXmlHttpRequest()) {
            /**
             * @var $repository UserRepository
             */
            $repository = $this->getDoctrine()->getManager()->getRepository(User::class);
            $data = $repository->getLike($input);
            return new JsonResponse(array("data" => json_encode($data)));
        } else {
            throw new HttpException('500', 'Invalid call');
        }
    }

    /**
     * @Route("/admin/export/csv/{yearPaquetage}", name="export_csv_file")
     */
    public function exportCsvFile($yearPaquetage = null)
    {
        $em = $this->getDoctrine()->getManager();

//        $date = new \DateTime();
//        $year = $date->format('Y');
//        $yearPaquetage = intval($year) + 1;

        $userOrderByCategories = $em->getRepository(UserOrderByCategory::class)->findBy(
            ['yearPaquetage' => $yearPaquetage]
        );

        $arrayExport = [];


            foreach ($userOrderByCategories as $index=>$userOrder) {
                $arrayExport[] = [];
                array_push($arrayExport[$index], $userOrder->getUser()->getMatricule(),
                    $userOrder->getUser()->getLastname(),
                    $userOrder->getUser()->getFirstname(),
                    $userOrder->getUser()->getStatut()->getName(),
                    $userOrder->getUser()->getQualification()->getName(),
                    $userOrder->getUser()->getAgence()->getName(),
                    $userOrder->getUser()->getDirection()->getName());
                foreach ($userOrder->getArrayByCategory() as $categoryOrder) {
                    array_push($arrayExport[$index], $categoryOrder['taille'], $categoryOrder['qty']);
                }
            }


        $writer = $this->container->get('egyg33k.csv.writer');
        $csv = $writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['matricule', 'nom','prenom','statut','qualification', 'etablissement','direction',
            'PANTALON ETE taille','PANTALON ETE qte',
            'PANTALON HIVER taille', 'PANTALON HIVER qte',
            'SWEAT SHIRT taille', 'SWEAT SHIRT qte',
            'TEE-SHIRT CLASSIQUE taille','TEE-SHIRT CLASSIQUE qte',
            'TSHIRT MICRO AERE taille','TSHIRT MICRO AERE qte',
            'POLO CHEF GUYANE taille','POLO CHEF GUYANE qte',
            'POLO CHEF D\'EQUIPE taille','POLO CHEF D\'EQUIPE qte',
            'CHEMISETTE MAITRISE taille','CHEMISETTE MAITRISE qte',
            'CHAUSSURES RANGERS JALATTE taille','CHAUSSURES RANGERS JALATTE qte',
            'CHAUSSURES RANGERS LEMAITRE taille', 'CHAUSSURES RANGERS LEMAITRE qte',
            'CHAUSSURES RANGERS SWIMM taille', 'CHAUSSURES RANGERS SWIMM qte',
            'PARKA taille','PARKA qte','TENUE DE PLUIE taille','TENUE DE PLUIE qte',
            'CEINTURE taille','CEINTURE qte','CASQUE taille','CASQUE qte','CHAUSSURES RANGERS JORAN taille',
            'CHAUSSURES RANGERS JORAN qte','CHAUSSURES RANGERS JALBORG taille', 'CHAUSSURES RANGERS JALBORG qte',
            'CHAUSSURES RANGERS JALOSBERN taille', 'CHAUSSURES RANGERS JALOSBERN qte',
            'CHAUSSURES RANGERS JALOSBERN Hiver taille', 'CHAUSSURES RANGERS JALOSBERN Hiver qte',
            'VESTE HIVER taille','VESTE HIVER qte','CADENAS taille','CADENAS qte','CHAUSSETTES taille','CHAUSSETTES qte',
            'CALECON taille','CALECON qte','TSHIRT manches longues taille','TSHIRT manches longues qte']);

        foreach ($arrayExport as $userOrder){
                $csv->insertOne($userOrder);
        }
        $csv->output('exportpaquetage.csv');
        die('end');


//        return $this->render('admin/exportCsv.html.twig', array());

    }

    /**
     * @Route("/admin/export/synthesecsv/{yearPaquetage2}", name="export_csv_synthese_file")
     */
    public function exportCsvSyntheseFile($yearPaquetage2 = null)
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository(Category::class)->findAll();
        $arrayCategory = [];
        foreach ($categories as $category) {
            array_push($arrayCategory,$category->getId());
        }

        $arrayCategoryTailles = [];

        foreach ($arrayCategory as $categoryId) {
            $tailles = $em->getRepository(Taille::class)->searchTailleByCategory($categoryId);
            foreach ($tailles as $taille) {
                $arrayCategoryTailles[$categoryId][$taille->getId()]['tailleId'] = $taille->getId();
                $arrayCategoryTailles[$categoryId][$taille->getId()]['tailleName'] = $taille->getName();
                $arrayCategoryTailles[$categoryId][$taille->getId()]['qte'] = 0;
            }
        }

        $arrayByCategoryByTaille=[];

        foreach ($arrayCategoryTailles as $categoryId=>$value) {
            $categoryName = $em->getRepository(Category::class)->find($categoryId)->getName();
            foreach ($value as $tailleId=>$valueByTailleId) {
                $arrayByCategoryByTaille[$categoryId][$tailleId] = [$categoryId,$categoryName,
                    $valueByTailleId['tailleName'],$valueByTailleId['qte']];
            }
        }

        $allOrderProducts = $em->getRepository(ProductPackage::class)->findBy(['yearPaquetage' => $yearPaquetage2]);

        $tab = [];
        $array = [];
        foreach ($allOrderProducts as $orderline) {
            $idpdtunique = $orderline->getCategoryIdTaille();
            $qty = $orderline->getQty();
            $array[$idpdtunique]['libelle'] = $orderline->getLibellePdt();
            $array[$idpdtunique]['taille'] = $orderline->getTaille();
            $array[$idpdtunique]['tailleId'] = $orderline->getTailleId();
            $array[$idpdtunique]['categoryId'] = $orderline->getCategoryId();
            $array[$idpdtunique]['categoryName'] = $orderline->getCategoryName();
            if (!array_key_exists($idpdtunique, $tab)) {
                $tab[$idpdtunique] = $qty;
                $array[$idpdtunique]['qty'] = $tab[$idpdtunique];
            } else {
                $tab[$idpdtunique] = $tab[$idpdtunique] + $qty;
                $array[$idpdtunique]['qty'] = $tab[$idpdtunique];
            }
        }

        foreach ($arrayByCategoryByTaille as $categoryId=>$value) {
            foreach ($value as $tailleId=>$valueByCategoryByTailleId) {
                foreach($array as $idpdtunique=>$valueByIdpdtunique) {
                    if ($categoryId == $valueByIdpdtunique['categoryId'] && $tailleId == $valueByIdpdtunique['tailleId']) {
                        $arrayByCategoryByTaille[$categoryId][$tailleId][3]  = $valueByIdpdtunique['qty'];
                    }
                }
            }
        }

        $writer = $this->container->get('egyg33k.csv.writer');
        $csv = $writer::createFromFileObject(new \SplTempFileObject());


        foreach ($arrayByCategoryByTaille as $categoryId=>$value){
            $csv->insertOne(['Categorie Id','Category Nom','taille','qte']);
            foreach($value as $tailleId=>$valueByCategoryByTailleId){
                $csv->insertOne($valueByCategoryByTailleId);
            }
        }
        $csv->output('exportsynthesepaquetage.csv');
        die('end');


//        return $this->render('admin/exportCsvSynthese.html.twig', array());
    }

    /**
     * @Route("/admin/export/allCommandeUser", name="pdf_export_allCommandeUser")
     */
    public function exportAllCommandeUser()
    {
        $em = $this->getDoctrine()->getManager();

        $yearPaquetage = 2019;

        $allCommandeUser = $em->getRepository(Commande::class)->findBy(array('yearPaquetage' => $yearPaquetage));

        $html = $this->renderView('pdf/pdfExportAllCommandeUser.html.twig', array(
            'allCommandeUser' => $allCommandeUser
        ));

        $filename = sprintf('allcommande2018-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );



    }

}
