<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParentCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Parentcategory controller.
 *
 * @Route("parentcategory")
 */
class ParentCategoryController extends Controller
{
    /**
     * Lists all parentCategory entities.
     *
     * @Route("/", name="parentcategory_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $parentCategories = $em->getRepository('AppBundle:ParentCategory')->findAll();

        return $this->render('parentcategory/index.html.twig', array(
            'parentCategories' => $parentCategories,
        ));
    }

    /**
     * Creates a new parentCategory entity.
     *
     * @Route("/new", name="parentcategory_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $parentCategory = new Parentcategory();
        $form = $this->createForm('AppBundle\Form\ParentCategoryType', $parentCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parentCategory);
            $em->flush();

            return $this->redirectToRoute('parentcategory_show', array('id' => $parentCategory->getId()));
        }

        return $this->render('parentcategory/new.html.twig', array(
            'parentCategory' => $parentCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a parentCategory entity.
     *
     * @Route("/{id}", name="parentcategory_show")
     * @Method("GET")
     */
    public function showAction(ParentCategory $parentCategory)
    {
        $deleteForm = $this->createDeleteForm($parentCategory);

        return $this->render('parentcategory/show.html.twig', array(
            'parentCategory' => $parentCategory,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing parentCategory entity.
     *
     * @Route("/{id}/edit", name="parentcategory_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ParentCategory $parentCategory)
    {
        $deleteForm = $this->createDeleteForm($parentCategory);
        $editForm = $this->createForm('AppBundle\Form\ParentCategoryType', $parentCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parentcategory_edit', array('id' => $parentCategory->getId()));
        }

        return $this->render('parentcategory/edit.html.twig', array(
            'parentCategory' => $parentCategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a parentCategory entity.
     *
     * @Route("/{id}", name="parentcategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ParentCategory $parentCategory)
    {
        $form = $this->createDeleteForm($parentCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parentCategory);
            $em->flush();
        }

        return $this->redirectToRoute('parentcategory_index');
    }

    /**
     * Creates a form to delete a parentCategory entity.
     *
     * @param ParentCategory $parentCategory The parentCategory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ParentCategory $parentCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parentcategory_delete', array('id' => $parentCategory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
