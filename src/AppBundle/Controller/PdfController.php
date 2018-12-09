<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\ProductPackage;
use AppBundle\Entity\User;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends Controller
{

    /**
     * @Route("/pdf", name="pdf")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $commandes = $em->getRepository(Commande::class)->findBy(array('user' => $user));
        $commande = $commandes[count($commandes) - 1];


        $html = $this->renderView('pdf/pdf.html.twig', array(
            'user' => $user,
            'commande' => $commande,
        ));

        $filename = sprintf('commande-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     * @Route("/pdf/export/{searchform}", name="pdf_export")
     */
    public function exportPdfAction($searchform)
    {
        $em = $this->getDoctrine()->getManager();

        $array = [];

        if ($searchform === 'all') {
            $allOrderProducts = $em->getRepository(ProductPackage::class)->findAll();
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

        $html = $this->renderView('pdf/pdfExport.html.twig', array(
            'orderpdts' => $array,
        ));

        $filename = sprintf('commande-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     *@Route("/pdf/export/{agence}/{paquetageType}", name="pdf_export_agence")
     */
    public function exportAgencePdfAction($agence = null ,$paquetageType = null )
    {
        $em = $this->getDoctrine()->getManager();

        $orderproductsSearch = $em->getRepository(ProductPackage::class)->searchOrderLineBy($agence,$paquetageType);


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


        $html = $this->renderView('pdf/pdfExport.html.twig', array(
            'orderpdts' => $array,
        ));

        $filename = sprintf('commande-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     *@Route("/pdf/export/{paquetageType}/{agence}", name="pdf_export_paquetageType")
     */
    public function exportPaquetageTypePdfAction($agence = null ,$paquetageType = null )
    {
        $em = $this->getDoctrine()->getManager();

        $orderproductsSearch = $em->getRepository(ProductPackage::class)->searchOrderLineBy($agence,$paquetageType);

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



        $html = $this->renderView('pdf/pdfExport.html.twig', array(
            'orderpdts' => $array,
        ));

        $filename = sprintf('commande-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     *@Route("/pdf/export/commandeUser/{userSearchId}/{userYearPaquetage}", name="pdf_export_commandeUser")
     */
    public function exportCommandeUser($userSearchId,$userYearPaquetage)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($userSearchId);

        $commandesUser = $em->getRepository(Commande::class)->findBy(array('user' => $userSearchId,
            'yearPaquetage' => $userYearPaquetage));
        $commandeUser = $commandesUser[0];


        $html = $this->renderView('pdf/pdfExportCommandeUser.html.twig', array(
            'user' => $user,
            'commandeUser' => $commandeUser
        ));

        $filename = sprintf('commande-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }
}
