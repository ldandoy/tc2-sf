<?php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Product;
use App\Form\ProductType;

/**
 * @Route("/products")
 * @IsGranted("ROLE_USER")
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
}