<?php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Product;
use App\Form\ProductType;

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="products_list")
     */
    public function list(): Response
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/{productId}/show", name="products_show")
     */
    public function show($productId): Response
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($productId);

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
    
    /**
     * @Route("/new", name="products_new")
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

            return $this->redirectToRoute('products_list');
        }

        return $this->renderForm('product/new.html.twig', [
            'form' => $form,
        ]);
    }
}