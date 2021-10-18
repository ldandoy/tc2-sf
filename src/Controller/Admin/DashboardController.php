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
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', []);
    }
}