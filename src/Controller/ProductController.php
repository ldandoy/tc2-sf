<?php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Product;
use App\Entity\CategoryProduct;
use App\Form\ProductType;

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="products_list")
     */
    public function list(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {
        $dql   = "SELECT p FROM App\Entity\Product p";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        $categoriesProduct = $em->getRepository(CategoryProduct::class)->findAll();

        /*$products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        */
        return $this->render('product/list.html.twig', [
            'pagination' => $pagination,
            'categories' => $categoriesProduct
        ]);
    }

    /**
     * @Route("/{productId}/show", name="products_show")
     */
    public function show($productId, SessionInterface $session): Response
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($productId);
        
        $cartItems = $session->get('cart', []);

        $qty = 1;
        foreach($cartItems as $key => $item) {
            if ($item['product']->getId() == $productId) {
                $qty = $item['productQty'];
            }
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'qtyProduct' => $qty
        ]);
    }
}