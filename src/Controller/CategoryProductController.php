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

use App\Entity\Product;
use App\Entity\CategoryProduct;

/**
 * @Route("/categories", name="categories_")
 */
class CategoryProductController extends AbstractController
{
    /**
     * @Route("/{categoryId}/products", name="products")
     */
    public function show(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator, $categoryId): Response
    {
        $dql   = "SELECT p FROM App\Entity\Product p WHERE p.CategoryProduct = :categoryId";
        $query = $em->createQuery($dql)->setParameter('categoryId', $categoryId);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );
        
        $categoriesProduct = $em->getRepository(CategoryProduct::class)->findAll();

        return $this->render('category_product/show.html.twig', [
            'pagination'    => $pagination,
            'categoryId'    => $categoryId,
            'categories'    => $categoriesProduct
        ]);
    }
}