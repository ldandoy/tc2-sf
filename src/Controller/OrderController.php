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

use App\Entity\Order;

/**
 * @Route("/orders", name="orders_")
 */
class OrderController extends AbstractController
{
/**
     * @Route("/", name="list")
     */
    public function list(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {
        $dql   = "SELECT o FROM App\Entity\Order o";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

        return $this->render('order/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}