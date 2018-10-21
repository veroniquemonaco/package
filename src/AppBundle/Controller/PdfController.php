<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\ProductPackage;
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
        } else {
            $orderproductsSearch = $em->getRepository(ProductPackage::class)->searchOrderLineBy($searchform);

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
}
