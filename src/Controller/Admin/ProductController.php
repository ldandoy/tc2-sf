<?php
// src/Controller/ProductController.php
namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Product;
use App\Form\ProductType;

/**
 * @Route("/admin/products")
 * @IsGranted("ROLE_ADMIN")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="admin_products_list")
     */
    public function list(): Response
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('admin/product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/{productId}/show", name="admin_products_show")
     */
    public function show($productId): Response
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($productId);

        return $this->render('admin/product/show.html.twig', [
            'product' => $product
        ]);
    }
    
    /**
     * @Route("/new", name="admin_products_new")
     */
    public function new(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_products_list');
        }

        return $this->renderForm('admin/product/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{productId}/edit", name="admin_products_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit($productId, Request $request): Response
    {
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_products_list');
        }

        return $this->renderForm('admin/product/edit.html.twig', [
            'form' => $form,
            'product' => $product
        ]);
    }

     /**
     * @Route("/{productId}/delete", name="admin_products_delete")
     */
    public function delete($productId, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->find($productId);
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('admin_products_list');
    }
}