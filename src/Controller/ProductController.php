<?php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

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
    public function list(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT p FROM App\Entity\Product p";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );


        /*$products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        */
        return $this->render('product/list.html.twig', [
            'pagination' => $pagination
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